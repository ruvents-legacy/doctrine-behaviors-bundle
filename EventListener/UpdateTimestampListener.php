<?php

declare(strict_types=1);

namespace Ruwork\DoctrineBehaviorsBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Ruwork\DoctrineBehaviorsBundle\Metadata\MetadataFactoryInterface;
use Ruwork\DoctrineBehaviorsBundle\Strategy\TimestampStrategy\TimestampStrategyInterface;

class UpdateTimestampListener implements EventSubscriber
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
            Events::preUpdate,
        ];
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        $class = get_class($entity);
        $metadata = $args->getEntityManager()->getClassMetadata($class);
        $timestamps = $this->factory->getMetadata($class)->getUpdateTimestamps();

        foreach ($timestamps as $property => $timestamp) {
            if ($timestamp->overwrite || !$metadata->getFieldValue($entity, $property)) {
                $value = $this->strategy->getTimestamp($metadata, $property);
                $metadata->setFieldValue($entity, $property, $value);
            }
        }
    }
}
