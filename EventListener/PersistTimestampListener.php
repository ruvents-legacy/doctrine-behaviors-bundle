<?php

declare(strict_types=1);

namespace Ruvents\DoctrineBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Ruvents\DoctrineBundle\Metadata\MetadataFactoryInterface;
use Ruvents\DoctrineBundle\Strategy\TimestampStrategy\TimestampStrategyInterface;

class PersistTimestampListener implements EventSubscriber
{
    private $factory;

    private $strategy;

    public function __construct(MetadataFactoryInterface $factory, TimestampStrategyInterface $strategy)
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

        foreach ($this->factory->getMetadata($class)->getPersistTimestamps() as $property => $persistTimestamp) {
            if ($persistTimestamp->overwrite || !$entityMetadata->getFieldValue($entity, $property)) {
                $value = $this->strategy->getTimestamp($entityMetadata, $property);
                $entityMetadata->setFieldValue($entity, $property, $value);
            }
        }
    }
}
