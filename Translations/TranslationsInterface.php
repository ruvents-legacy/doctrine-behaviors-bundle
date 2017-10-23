<?php

namespace Ruvents\DoctrineBundle\Translations;

interface TranslationsInterface
{
    /**
     * @return string[]
     */
    public static function getLocales(): array;

    public function setCurrentLocale(string $locale);
}
