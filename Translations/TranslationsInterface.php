<?php

namespace Ruvents\DoctrineBundle\Translations;

interface TranslationsInterface
{
    /**
     * @return array with locale keys and true values
     *               ['en' => true, 'ru' => true]
     */
    public static function getLocalesMap(): array;

    public function setCurrentLocale(string $locale);
}
