<?php

namespace Ruvents\DoctrineBundle\Mapping\Metadata;

use Ruvents\DoctrineBundle\Mapping\Timestamp;

interface TimestampMetadataInterface extends ClassMetadataInterface
{
    /**
     * @param string    $property
     * @param Timestamp $timestamp
     */
    public function addTimestampProperty($property, Timestamp $timestamp);

    /**
     * @return Timestamp[]
     */
    public function getTimestampProperties();

    /**
     * @param string $property
     *
     * @return bool
     */
    public function isTimestampProperty($property);
}
