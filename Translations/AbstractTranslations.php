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
        $this->ensureNonEmptyCurrentLocale();
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentLocale(string $currentLocale)
    {
        if (isset($this->getLocalesMap()[$currentLocale])) {
            $this->currentLocale = $currentLocale;

            return;
        }

        $this->ensureNonEmptyCurrentLocale();
    }

    public function getCurrent(bool $fallback = true)
    {
        $this->ensureNonEmptyCurrentLocale();

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
        foreach ($this->getLocalesMap() as $locale => $nb) {
            yield $locale => $this->$locale;
        }
    }

    private function ensureNonEmptyCurrentLocale()
    {
        if (null !== $this->currentLocale) {
            return;
        }

        $localesMap = $this->getLocalesMap();
        $this->currentLocale = key($localesMap);
    }
}
