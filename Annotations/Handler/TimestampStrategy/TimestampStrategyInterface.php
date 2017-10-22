<?php

namespace Ruvents\DoctrineBundle\Annotations\Handler\TimestampStrategy;

interface TimestampStrategyInterface
{
    public function getTimestamp(): \DateTimeInterface;
}
