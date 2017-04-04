<?php

namespace Ruvents\DoctrineBundle\Mapping\Metadata;

use Ruvents\DoctrineBundle\Mapping\Translatable;

interface TranslatableMetadataInterface extends ClassMetadataInterface
{
    /**
     * @param string       $property
     * @param Translatable $mapping
     */
    public function addTranslatableMapping($property, Translatable $mapping);

    /**
     * @return Translatable[]
     */
    public function getTranslatableMappings();

    /**
     * @param string $property
     *
     * @return bool
     */
    public function hasTranslatableMapping($property);
}
