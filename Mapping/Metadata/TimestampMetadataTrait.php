<?php

namespace Ruvents\DoctrineBundle\Mapping\Metadata;

use Ruvents\DoctrineBundle\Mapping\Timestamp;

trait TimestampMetadataTrait
{
    /**
     * @var Timestamp[]
     */
    public $timestampProperties = [];

    /**
     * @see TimestampMetadataInterface::addTimestampProperty
     */
    public function addTimestampProperty($property, Timestamp $timestamp)
    {
        $this->timestampProperties[$property] = $timestamp;
    }

    /**
     * @see TimestampMetadataInterface::getTimestampProperties
     */
    public function getTimestampProperties()
    {
        return $this->timestampProperties;
    }

    /**
     * @see TimestampMetadataInterface::isTimestampProperty
     */
    public function isTimestampProperty($property)
    {
        return isset($this->timestampProperties[$property]);
    }
}
