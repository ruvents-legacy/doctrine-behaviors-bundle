<?php

namespace Ruvents\DoctrineBundle;

interface TranslatorInterface
{
    /**
     * @param object $entity
     * @param string $locale
     */
    public function translate($entity, $locale);

    /**
     * @param object $entity
     * @param string $property
     * @param string $locale
     */
    public function translateProperty($entity, $property, $locale);
}
