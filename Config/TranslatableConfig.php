<?php

namespace Ruvents\DoctrineBundle\Config;

use Ruvents\DoctrineBundle\Annotation\Translatable;

class TranslatableConfig
{
    /**
     * @var string
     */
    public $propertyPath;

    /**
     * @param Translatable $annotation
     */
    public function __construct(Translatable $annotation)
    {
        $this->propertyPath = $annotation->propertyPath;
    }
}
