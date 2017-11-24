<?php

namespace Ruvents\DoctrineBundle\Validator;

use Symfony\Component\Validator\Constraints\Composite as AbstractComposite;

/**
 * @Annotation()
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class Composite extends AbstractComposite
{
    public $constraints;

    /**
     * {@inheritdoc}
     */
    public function getDefaultOption()
    {
        return 'constraints';
    }

    /**
     * {@inheritdoc}
     */
    protected function getCompositeOption()
    {
        return 'constraints';
    }
}
