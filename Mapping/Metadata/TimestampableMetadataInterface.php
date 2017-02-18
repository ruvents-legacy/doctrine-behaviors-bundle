<?php

namespace Ruvents\DoctrineBundle\Mapping\Metadata;

use Ruvents\DoctrineBundle\Mapping\Timestampable;

interface TimestampableMetadataInterface extends ClassMetadataInterface
{
    /**
     * @param string        $property
     * @param Timestampable $config
     */
    public function addTimestampableConfig($property, Timestampable $config);

    /**
     * @return Timestampable[]
     */
    public function getTimestampablePropertiesConfigs();

    /**
     * @param string $property
     *
     * @return bool
     */
    public function isPropertyTimestampable($property);
}
