<?php

declare(strict_types=1);

namespace Ruvents\DoctrineBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Ruvents\DoctrineBundle\Metadata\MetadataFactoryInterface;
use Ruvents\DoctrineBundle\Strategy\AuthorStrategy\AuthorStrategyInterface;

class AuthorListener implements EventSubscriber
{
    private $factory;

    private $strategy;

    public function __construct(MetadataFactoryInterface $factory, AuthorStrategyInterface $strategy)
    {
        $this->factory = $factory;
        $this->strategy = $strategy;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        $class = get_class($entity);
        $entityMetadata = $args->getEntityManager()->getClassMetadata($class);

        foreach ($this->factory->getMetadata($class)->getAuthors() as $property => $author) {
            if ($author->overwrite || !$entityMetadata->getFieldValue($entity, $property)) {
                $value = $this->strategy->getAuthor($entityMetadata, $property);
                $entityMetadata->setFieldValue($entity, $property, $value);
            }
        }
    }
}
