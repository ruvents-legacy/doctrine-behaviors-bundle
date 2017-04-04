<?php

namespace Ruvents\DoctrineBundle\Mapping\Metadata;

use Ruvents\DoctrineBundle\Mapping\Timestamp;

interface TimestampMetadataInterface extends ClassMetadataInterface
{
    /**
     * @param string    $property
     * @param Timestamp $mapping
     */
    public function addTimestampMapping($property, Timestamp $mapping);

    /**
     * @return Timestamp[]
     */
    public function getTimestampMappings();

    /**
     * @param string $property
     *
     * @return bool
     */
    public function hasTimestampMapping($property);
}
