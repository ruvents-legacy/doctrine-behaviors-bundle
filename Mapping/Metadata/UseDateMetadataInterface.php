<?php

namespace Ruvents\DoctrineBundle\Mapping\Metadata;

use Ruvents\DoctrineBundle\Mapping\UseDate;

interface UseDateMetadataInterface extends ClassMetadataInterface
{
    /**
     * @param string $property
     * @param UseDate $mapping
     */
    public function addUseDateMapping($property, UseDate $mapping);

    /**
     * @return UseDate[]
     */
    public function getUseDateMappings();

    /**
     * @param string $property
     *
     * @return bool
     */
    public function hasUseDateMapping($property);
}
