<?php

namespace Ruvents\DoctrineBundle\Mapping\Metadata;

use Ruvents\DoctrineBundle\Mapping\Timestampable;

trait TimestampableMetadataTrait
{
    /**
     * @var Timestampable[]
     */
    public $timestampablePropertiesConfigs;

    /**
     * @see TimestampableMetadataInterface::addTimestampableConfig
     */
    public function addTimestampableConfig($property, Timestampable $config)
    {
        $this->timestampablePropertiesConfigs[$property] = $config;
    }

    /**
     * @see TimestampableMetadataInterface::getTimestampablePropertiesConfigs
     */
    public function getTimestampablePropertiesConfigs()
    {
        return $this->timestampablePropertiesConfigs;
    }

    /**
     * @see TimestampableMetadataInterface::isPropertyTimestampable
     */
    public function isPropertyTimestampable($property)
    {
        return isset($this->timestampablePropertiesConfigs[$property]);
    }
}
