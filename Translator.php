<?php

namespace Ruvents\DoctrineBundle;

use Ruvents\DoctrineBundle\Mapping\Factory\ClassMetadataFactoryInterface;
use Ruvents\DoctrineBundle\Mapping\Metadata\TranslatableMetadataInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\PropertyPath;

class Translator implements TranslatorInterface
{
    /**
     * @var ClassMetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @var string[]
     */
    private $fallbackLocales;

    /**
     * @var PropertyAccessor
     */
    private $accessor;

    /**
     * @var PropertyPath[][][]
     */
    private $cachedLocalePropertyPaths = [];

    public function __construct(
        ClassMetadataFactoryInterface $metadataFactory,
        array $fallbackLocales = [],
        PropertyAccessor $accessor = null
    ) {
        $this->metadataFactory = $metadataFactory;
        $this->fallbackLocales = $fallbackLocales;
        $this->accessor = $accessor ?: PropertyAccess::createPropertyAccessor();
    }

    /**
     * {@inheritdoc}
     */
    public function translate($entity, $locale)
    {
        if ($this->metadataFactory->hasMetadataFor($entity)) {
            $metadata = $this->metadataFactory->getMetadataFor($entity);

            if ($metadata instanceof TranslatableMetadataInterface) {
                foreach ($metadata->getTranslatableMappings() as $property => $mapping) {
                    $this->translateProperty($entity, $property, $locale);
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function translateProperty($entity, $property, $locale)
    {
        $metadata = $this->metadataFactory->getMetadataFor($entity);

        if (!$metadata instanceof TranslatableMetadataInterface) {
            throw new \Exception();
        }

        if (!$metadata->hasTranslatableMapping($property)) {
            throw new \Exception();
        }

        $mapping = $metadata->getTranslatableMappings()[$property];

        $locales = $this->fallbackLocales;
        array_unshift($locales, $locale);
        $locales = array_unique($locales);

        foreach ($locales as $locale) {
            $propertyPath = $this->getLocalePropertyPath(
                $metadata->getName(),
                $property,
                $mapping->propertyPath,
                $mapping->map,
                $locale
            );

            if ($this->hasValue($entity, $propertyPath, $mapping->fallbackIfNull)) {
                $reflectionProperty = $metadata->getReflectionClass()->getProperty($property);

                if (!$reflectionProperty->isPublic()) {
                    $reflectionProperty->setAccessible(true);
                }

                $reflectionProperty->setValue($entity, $this->accessor->getValue($entity, $propertyPath));

                return;
            }

            if (!$mapping->fallback) {
                return;
            }
        }
    }

    /**
     * @param object       $entity
     * @param PropertyPath $propertyPath
     * @param bool         $fallbackIfNull
     *
     * @return bool
     */
    private function hasValue($entity, PropertyPath $propertyPath, $fallbackIfNull)
    {
        if (!$this->accessor->isReadable($entity, $propertyPath)) {
            return false;
        }

        if ($fallbackIfNull && null === $this->accessor->getValue($entity, $propertyPath)) {
            return false;
        }

        return true;
    }

    /**
     * @param string   $class
     * @param string   $property
     * @param string   $propertyPathTemplate
     * @param string[] $map
     * @param string   $locale
     *
     * @return PropertyPath
     */
    private function getLocalePropertyPath($class, $property, $propertyPathTemplate, array $map, $locale)
    {
        if ($class && $property && isset($this->cachedLocalePropertyPaths[$class][$property][$locale])) {
            return $this->cachedLocalePropertyPaths[$class][$property][$locale];
        }

        if (isset($map[$locale])) {
            $propertyPath = $map[$locale];
        } else {
            $propertyPath = str_replace(['%locale%', '%Locale%'], [$locale, ucfirst($locale)], $propertyPathTemplate);
        }

        return $this->cachedLocalePropertyPaths[$class][$property][$locale] = new PropertyPath($propertyPath);
    }
}
