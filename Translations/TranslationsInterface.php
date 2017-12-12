<?php

declare(strict_types=1);

namespace Ruvents\DoctrineBundle\Translations;

interface TranslationsInterface
{
    public function has(string $locale): bool;

    public function get(string $locale = null);

    public function setCurrentLocale(string $locale);
}
