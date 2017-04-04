<?php

namespace Ruvents\DoctrineBundle\Mapping\Metadata;

use Ruvents\DoctrineBundle\Mapping\UseDate;

trait UseDateMetadataTrait
{
    /**
     * @var UseDate[]
     */
    public $useDateMappings = [];

    /**
     * @see UseDateMetadataInterface::addUseDateMapping
     */
    public function addUseDateMapping($property, UseDate $mapping)
    {
        $this->useDateMappings[$property] = $mapping;
    }

    /**
     * @see UseDateMetadataInterface::getUseDateMappings
     */
    public function getUseDateMappings()
    {
        return $this->useDateMappings;
    }

    /**
     * @see UseDateMetadataInterface::hasUseDateMapping
     */
    public function hasUseDateMapping($property)
    {
        return isset($this->useDateMappings[$property]);
    }
}
