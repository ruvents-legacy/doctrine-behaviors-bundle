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
        $this->resetCurrentLocale();
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

        if (null === $this->currentLocale) {
            $this->resetCurrentLocale();
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
        foreach ($this->getLocalesMap() as $locale => $nb) {
            yield $locale => $this->$locale;
        }
    }

    private function resetCurrentLocale()
    {
        $localesMap = $this->getLocalesMap();
        $this->currentLocale = key($localesMap);
    }
}
