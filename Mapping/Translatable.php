<?php

namespace Ruvents\DoctrineBundle\Mapping;

/**
 * @Annotation()
 * @Target({"PROPERTY"})
 */
class Translatable
{
    /**
     * @Required
     * @var string
     */
    public $propertyPath;

    /**
     * @var array<string>
     */
    public $map = [];

    /**
     * @var bool
     */
    public $fallback = true;

    /**
     * @var bool
     */
    public $fallbackIfNull = true;
}
