<?php

namespace Ruvents\DoctrineBundle\Annotations\Handler;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Ruvents\DoctrineBundle\Annotations\Handler\TimestampStrategy\TimestampStrategyInterface;
use Ruvents\DoctrineBundle\Annotations\Map;
use Ruvents\DoctrineBundle\Annotations\Mapping\UpdateTimestamp;

class UpdateTimestampHandler implements HandlerInterface
{
    /**
     * @var TimestampStrategyInterface
     */
    private $strategy;

    public function __construct(TimestampStrategyInterface $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * {@inheritdoc}
     */
    public static function supportsAnnotation($annotation, int $type): bool
    {
        return self::TYPE_PROPERTY === $type && $annotation instanceof UpdateTimestamp;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Events::preUpdate,
        ];
    }

    public function preUpdate(LifecycleEventArgs $args, Map $map)
    {
        $entity = $args->getEntity();
        $metadata = $args->getEntityManager()->getClassMetadata(get_class($entity));

        foreach ($map->getPropertyAnnotations() as $property => $annotations) {
            $metadata->setFieldValue($entity, $property, $this->strategy->getTimestamp());
        }
    }
}
