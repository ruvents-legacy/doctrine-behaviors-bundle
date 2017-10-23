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
        $locales = $this->getLocales();

        if ([] === $locales) {
            throw new \LogicException('The TranslationsInterface::getLocales() method must return at least one value.');
        }

        $defaultLocale = \Locale::getDefault();

        if (in_array($defaultLocale, $locales, true)) {
            $this->currentLocale = $defaultLocale;
        } else {
            $this->currentLocale = reset($locales);
        }
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
