<?php

namespace Ruvents\DoctrineBundle\Validator;

use Doctrine\Common\Annotations\Annotation\Target;
use Symfony\Component\Validator\Constraints\Composite;

/**
 * @Annotation()
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class Translations extends Composite
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

    /**
     * {@inheritdoc}
     */
    protected function getCompositeOption()
    {
        return 'locales';
    }
}
