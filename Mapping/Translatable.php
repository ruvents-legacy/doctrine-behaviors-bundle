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
    public $path;

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
