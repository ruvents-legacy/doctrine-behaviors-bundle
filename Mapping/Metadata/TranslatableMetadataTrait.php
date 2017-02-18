<?php

namespace Ruvents\DoctrineBundle\Mapping\Metadata;

use Ruvents\DoctrineBundle\Mapping\Translatable;

trait TranslatableMetadataTrait
{
    /**
     * @var Translatable[]
     */
    public $translatablePropertiesConfigs;

    /**
     * @see TranslatableMetadataInterface::addTranslatableConfig
     */
    public function addTranslatableConfig($property, Translatable $config)
    {
        $this->translatablePropertiesConfigs[$property] = $config;
    }

    /**
     * @see TranslatableMetadataInterface::getTranslatablePropertiesConfigs
     */
    public function getTranslatablePropertiesConfigs()
    {
        return $this->translatablePropertiesConfigs;
    }

    /**
     * @see TranslatableMetadataInterface::isPropertyTranslatable
     */
    public function isPropertyTranslatable($property)
    {
        return isset($this->translatablePropertiesConfigs[$property]);
    }
}
