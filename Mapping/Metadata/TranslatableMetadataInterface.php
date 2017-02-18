<?php

namespace Ruvents\DoctrineBundle\Mapping\Metadata;

use Ruvents\DoctrineBundle\Mapping\Translatable;

interface TranslatableMetadataInterface extends ClassMetadataInterface
{
    /**
     * @param string       $property
     * @param Translatable $config
     */
    public function addTranslatableConfig($property, Translatable $config);

    /**
     * @return Translatable[]
     */
    public function getTranslatablePropertiesConfigs();

    /**
     * @param string $property
     *
     * @return bool
     */
    public function isPropertyTranslatable($property);
}
