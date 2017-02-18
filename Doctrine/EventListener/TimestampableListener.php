<?php

namespace Ruvents\DoctrineBundle\Doctrine\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Ruvents\DoctrineBundle\Mapping\Factory\ClassMetadataFactoryInterface;
use Ruvents\DoctrineBundle\Mapping\Metadata\TimestampableMetadataInterface;
use Ruvents\DoctrineBundle\Mapping\Timestampable;

class TimestampableListener implements EventSubscriber
{
    /**
     * @var ClassMetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @param ClassMetadataFactoryInterface $metadataFactory
     */
    public function __construct(ClassMetadataFactoryInterface $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
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
            $time = new \DateTime();

            if ($metadata instanceof TimestampableMetadataInterface) {
                foreach ($metadata->getTimestampablePropertiesConfigs() as $property => $config) {
                    if (Timestampable::ON_CREATE === $config->on
                        || Timestampable::ON_UPDATE === $config->on
                    ) {
                        $entityMetadata->setFieldValue($entity, $property, clone $time);
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
            $time = new \DateTime();
            $changed = false;

            if ($metadata instanceof TimestampableMetadataInterface) {
                foreach ($metadata->getTimestampablePropertiesConfigs() as $property => $config) {
                    if (Timestampable::ON_UPDATE === $config->on) {
                        $entityMetadata->setFieldValue($entity, $property, clone $time);
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
