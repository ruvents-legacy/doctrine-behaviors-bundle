<?php

namespace Ruvents\DoctrineBundle\Annotations\Handler\TimestampStrategy;

class ImmutableTimestampStrategy implements TimestampStrategyInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTimestamp(): \DateTimeInterface
    {
        return new \DateTimeImmutable();
    }
}
