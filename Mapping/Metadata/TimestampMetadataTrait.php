<?php

namespace Ruvents\DoctrineBundle\Mapping\Metadata;

use Ruvents\DoctrineBundle\Mapping\Timestamp;

trait TimestampMetadataTrait
{
    /**
     * @var Timestamp[]
     */
    public $timestampMappings = [];

    /**
     * @see TimestampMetadataInterface::addTimestampMapping
     */
    public function addTimestampMapping($property, Timestamp $mapping)
    {
        $this->timestampMappings[$property] = $mapping;
    }

    /**
     * @see TimestampMetadataInterface::getTimestampMappings
     */
    public function getTimestampMappings()
    {
        return $this->timestampMappings;
    }

    /**
     * @see TimestampMetadataInterface::hasTimestampMapping
     */
    public function hasTimestampMapping($property)
    {
        return isset($this->timestampMappings[$property]);
    }
}
