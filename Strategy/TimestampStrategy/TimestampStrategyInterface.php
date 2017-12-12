<?php

namespace Ruvents\DoctrineBundle\Strategy\TimestampStrategy;

use Doctrine\ORM\Mapping\ClassMetadata;

interface TimestampStrategyInterface
{
    public function getTimestamp(ClassMetadata $metadata, string $property): \DateTimeInterface;
}
