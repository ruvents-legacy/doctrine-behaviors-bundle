<?php

namespace Ruvents\DoctrineBundle\Validator;

use Doctrine\Common\Annotations\Annotation\Target;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation()
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class Translations extends Constraint
{
    /**
     * @var array
     */
    public $locales = [];

    public function getDefaultOption()
    {
        return 'locales';
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredOptions()
    {
        return ['locales'];
    }
}
