<?php

namespace Ruvents\DoctrineBundle\Translations;

abstract class AbstractTranslations implements TranslationsInterface, \IteratorAggregate
{
    /**
     * @var string
     */
    private $currentLocale;

    /**
     * {@inheritdoc}
     */
    public function setCurrentLocale(string $currentLocale)
    {
        if (isset($this->getLocalesMap()[$currentLocale])) {
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
        foreach ($this->getLocalesMap() as $locale => $nb) {
            yield $locale => $this->$locale;
        }
    }
}
