<?php

namespace Ruvents\DoctrineBundle\Mapping;

/**
 * @Annotation()
 * @Target({"PROPERTY"})
 */
class UseDate
{
    /**
     * @Required
     * @var string
     */
    public $propertyPath;
}
