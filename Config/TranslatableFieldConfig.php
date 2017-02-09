<?php

namespace Ruvents\DoctrineBundle\Config;

use Ruvents\DoctrineBundle\Annotation\TranslatableField;

class TranslatableFieldConfig
{
    /**
     * @var string[]
     */
    public $map = [];

    /**
     * @param TranslatableField $annotation
     */
    public function __construct(TranslatableField $annotation)
    {
        $this->map = $annotation->map ?: [];
    }
}
