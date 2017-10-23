<?php

namespace Ruvents\DoctrineBundle\Translations;

abstract class AbstractTranslations implements TranslationsInterface, \IteratorAggregate
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

        if ($value) {
            return $value;
        }

        if ($fallback) {
            foreach ($this as $value) {
                if ($value) {
                    return $value;
                }
            }
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): \Generator
    {
        foreach ($this->getLocales() as $locale) {
            yield $locale => $this->$locale;
        }
    }
}
