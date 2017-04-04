<?php

namespace Ruvents\DoctrineBundle\Mapping\Metadata;

use Ruvents\DoctrineBundle\Mapping\Translatable;

trait TranslatableMetadataTrait
{
    /**
     * @var Translatable[]
     */
    public $translatableMappings = [];

    /**
     * @see TranslatableMetadataInterface::addTranslatableMapping
     */
    public function addTranslatableMapping($property, Translatable $mapping)
    {
        $this->translatableMappings[$property] = $mapping;
    }

    /**
     * @see TranslatableMetadataInterface::getTranslatableMappings
     */
    public function getTranslatableMappings()
    {
        return $this->translatableMappings;
    }

    /**
     * @see TranslatableMetadataInterface::hasTranslatableMapping
     */
    public function hasTranslatableMapping($property)
    {
        return isset($this->translatableMappings[$property]);
    }
}
