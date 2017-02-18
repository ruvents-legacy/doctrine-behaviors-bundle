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
    private $cachedLocalePaths = [];

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
                foreach ($metadata->getTranslatablePropertiesConfigs() as $property => $config) {
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

        if (!$metadata->isPropertyTranslatable($property)) {
            throw new \Exception();
        }

        $config = $metadata->getTranslatablePropertiesConfigs()[$property];

        $locales = $this->fallbackLocales;
        array_unshift($locales, $locale);
        $locales = array_unique($locales);

        foreach ($locales as $locale) {
            $path = $this->getLocalePath($metadata->getName(), $property, $config->path, $config->map, $locale);

            if ($this->hasValue($entity, $path, $config->fallbackIfNull)) {
                $reflectionProperty = $metadata->getReflectionClass()->getProperty($property);

                if (!$reflectionProperty->isPublic()) {
                    $reflectionProperty->setAccessible(true);
                }

                $reflectionProperty->setValue($entity, $this->accessor->getValue($entity, $path));

                return;
            }

            if (!$config->fallback) {
                return;
            }
        }
    }

    /**
     * @param object       $entity
     * @param PropertyPath $path
     * @param bool         $fallbackIfNull
     *
     * @return bool
     */
    private function hasValue($entity, PropertyPath $path, $fallbackIfNull)
    {
        if (!$this->accessor->isReadable($entity, $path)) {
            return false;
        }

        if ($fallbackIfNull && null === $this->accessor->getValue($entity, $path)) {
            return false;
        }

        return true;
    }

    /**
     * @param string   $class
     * @param string   $property
     * @param string   $pathTemplate
     * @param string[] $map
     * @param string   $locale
     *
     * @return PropertyPath
     */
    private function getLocalePath($class, $property, $pathTemplate, array $map, $locale)
    {
        if ($class && $property && isset($this->cachedLocalePaths[$class][$property][$locale])) {
            return $this->cachedLocalePaths[$class][$property][$locale];
        }

        if (isset($map[$locale])) {
            $path = $map[$locale];
        } else {
            $path = str_replace(['%locale%', '%Locale%'], [$locale, ucfirst($locale)], $pathTemplate);
        }

        return $this->cachedLocalePaths[$class][$property][$locale] = new PropertyPath($path);
    }
}
