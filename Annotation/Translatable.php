<?php

namespace Ruvents\DoctrineBundle\Annotation;

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
}
