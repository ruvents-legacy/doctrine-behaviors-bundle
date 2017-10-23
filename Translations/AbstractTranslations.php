<?php

namespace Ruvents\DoctrineBundle\Translations;

abstract class AbstractTranslations implements TranslationsInterface
{
    /**
     * @var string
     */
    private $currentLocale;

    public function __construct()
    {
        $this->currentLocale = \Locale::getDefault();
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentLocale(string $currentLocale)
    {
        if (in_array($currentLocale, $this->getLocales(), true)) {
            $this->currentLocale = $currentLocale;
        }
    }

    public function getCurrent(bool $fallback = true)
    {
        $value = $this->{$this->currentLocale};

        if (!empty($value) || !$fallback) {
            return $value;
        }

        foreach ($this->getLocales() as $locale) {
            if (!empty($this->$locale)) {
                return $this->$locale;
            }
        }

        return $value;
    }
}
