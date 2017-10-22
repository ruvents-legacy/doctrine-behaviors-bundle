<?php

namespace Ruvents\DoctrineBundle\Annotations\Handler\TimestampStrategy;

class MutableTimestampStrategy implements TimestampStrategyInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTimestamp(): \DateTimeInterface
    {
        return new \DateTime();
    }
}
