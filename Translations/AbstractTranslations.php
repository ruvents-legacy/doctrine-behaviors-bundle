<?php

declare(strict_types=1);

namespace Ruvents\DoctrineBundle\Translations;

abstract class AbstractTranslations implements TranslationsInterface
{
    private $currentLocale;

    public function getCurrentLocale(): string
    {
        return $this->currentLocale ?? $this->getDefaultCurrentLocale();
    }

    public function setCurrentLocale(string $locale)
    {
        if ($this->has($locale)) {
            $this->currentLocale = $locale;
        }

        return $this;
    }

    public function has(string $locale): bool
    {
        return property_exists($this, $locale);
    }

    public function get(string $locale = null)
    {
        $locale = $locale ?? $this->getCurrentLocale();

        if (!$this->has($locale)) {
            throw new \OutOfBoundsException(sprintf('Class "%s" does not support locale "%s".', get_class($this), $locale));
        }

        return $this->$locale;
    }

    public function getCurrent(bool $fallback = true)
    {
        $currentLocale = $this->getCurrentLocale();
        $fallbackLocales = [$currentLocale => true];

        if ($fallback) {
            $fallbackLocales = $fallbackLocales + array_flip($this->getFallbackLocales());
        }

        foreach ($fallbackLocales as $locale => $nb) {
            if ($current = $this->get($locale)) {
                break;
            }
        }

        return $current ?? null;
    }

    public function set(string $locale, $value)
    {
        if (!$this->has($locale)) {
            throw new \OutOfBoundsException(sprintf('Class "%s" does not support locale "%s".', get_class($this), $locale));
        }

        $this->$locale = $value;

        return $this;
    }

    protected function getFallbackLocales(): array
    {
        return [];
    }

    abstract protected function getDefaultCurrentLocale(): string;
}
