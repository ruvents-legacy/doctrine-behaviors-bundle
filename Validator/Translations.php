<?php

namespace Ruvents\DoctrineBundle\Validator;

use Doctrine\Common\Annotations\Annotation\Target;
use Symfony\Component\Validator\Constraints\Composite as AbstractComposite;

/**
 * @Annotation()
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class Translations extends AbstractComposite
{
    /**
     * @var array
     */
    public $locales = [];

    public function __construct($options = null)
    {
        $locales = &$options['locales'];

        if (isset($options['value'])) {
            $locales = &$options['value'];
        }

        foreach ($locales as &$localeConstraints) {
            if (is_array($localeConstraints)) {
                $localeConstraints = new Composite(['constraints' => $localeConstraints]);
            }
        }

        parent::__construct($options);
    }

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
