<?php

namespace Ruvents\DoctrineBundle\Doctrine\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Ruvents\DoctrineBundle\AuthorProviderInterface;
use Ruvents\DoctrineBundle\Mapping\Factory\ClassMetadataFactoryInterface;
use Ruvents\DoctrineBundle\Mapping\Metadata\AuthorMetadataInterface;

class AuthorListener implements EventSubscriber
{
    /**
     * @var ClassMetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @var AuthorProviderInterface
     */
    private $provider;

    /**
     * @param ClassMetadataFactoryInterface $metadataFactory
     * @param AuthorProviderInterface       $provider
     */
    public function __construct(ClassMetadataFactoryInterface $metadataFactory, AuthorProviderInterface $provider)
    {
        $this->metadataFactory = $metadataFactory;
        $this->provider = $provider;
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function prePersist(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();

        if ($this->metadataFactory->hasMetadataFor($entity)) {
            $entityMetadata = $event->getEntityManager()->getClassMetadata(get_class($entity));
            $metadata = $this->metadataFactory->getMetadataFor($entity);

            if ($metadata instanceof AuthorMetadataInterface) {
                foreach ($metadata->getAuthorProperties() as $property => $author) {
                    if ($author->trackOnPersist()) {
                        $entityMetadata->setFieldValue($entity, $property, $this->provider->getAuthor());
                    }
                }
            }
        }
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function preUpdate(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();

        if ($this->metadataFactory->hasMetadataFor($entity)) {
            $entityMetadata = $event->getEntityManager()->getClassMetadata(get_class($entity));
            $metadata = $this->metadataFactory->getMetadataFor($entity);
            $changed = false;

            if ($metadata instanceof AuthorMetadataInterface) {
                foreach ($metadata->getAuthorProperties() as $property => $author) {
                    if ($author->trackOnUpdate()) {
                        $entityMetadata->setFieldValue($entity, $property, $this->provider->getAuthor());
                        $changed = true;
                    }
                }
            }

            if ($changed) {
                $event->getEntityManager()
                    ->getUnitOfWork()
                    ->recomputeSingleEntityChangeSet($entityMetadata, $entity);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }
}
