<?php

namespace Ruvents\DoctrineBundle\EventListener;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Ruvents\DoctrineBundle\Annotation\TranslatableField;
use Ruvents\DoctrineBundle\Config\TranslatableFieldConfig;

class TranslatableFieldDoctrineListener implements EventSubscriber
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var string
     */
    private $defaultLocale;

    /**
     * @var string
     */
    private $currentLocale;

    /**
     * @var TranslatableFieldConfig[][]
     */
    private $configs = [];

    /**
     * @param Reader $reader
     * @param string $defaultLocale
     */
    public function __construct(Reader $reader, $defaultLocale)
    {
        $this->reader = $reader;
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * @param string $currentLocale
     */
    public function setCurrentLocale($currentLocale)
    {
        $this->currentLocale = $currentLocale;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postLoad,
            Events::postPersist,
            Events::postUpdate,
        ];
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postLoad(LifecycleEventArgs $event)
    {
        $this->processEntity(
            $entity = $event->getEntity(),
            $event->getEntityManager()->getClassMetadata(get_class($event->getEntity()))
        );
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postPersist(LifecycleEventArgs $event)
    {
        $this->processEntity(
            $entity = $event->getEntity(),
            $event->getEntityManager()->getClassMetadata(get_class($event->getEntity()))
        );
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postUpdate(LifecycleEventArgs $event)
    {
        $this->processEntity(
            $entity = $event->getEntity(),
            $event->getEntityManager()->getClassMetadata(get_class($event->getEntity()))
        );
    }

    /**
     * @param object        $entity
     * @param ClassMetadata $metadata
     */
    private function processEntity($entity, ClassMetadata $metadata)
    {
        $configs = $this->getClassConfigs($metadata);
        $locale = $this->currentLocale ?: $this->defaultLocale;

        foreach ($configs as $property => $config) {
            $localeField = $this->getLocaleField($property, $locale, $this->defaultLocale, $config, $metadata);
            $reflectionProperty = $metadata->getReflectionClass()->getProperty($property);

            if (!$reflectionProperty->isPublic()) {
                $reflectionProperty->setAccessible(true);
            }

            $reflectionProperty->setValue($entity, $metadata->getFieldValue($entity, $localeField));
        }
    }

    /**
     * @param string                  $property
     * @param string                  $locale
     * @param string                  $defaultLocale
     * @param TranslatableFieldConfig $config
     * @param ClassMetadata           $metadata
     *
     * @return array|string|\string[]
     * @throws \RuntimeException
     */
    private function getLocaleField(
        $property,
        $locale,
        $defaultLocale,
        TranslatableFieldConfig $config,
        ClassMetadata $metadata
    ) {
        if (isset($config->map[$locale])) {
            if ($metadata->hasField($config->map[$locale])) {
                return $config->map[$locale];
            }

            throw new \RuntimeException(
                sprintf('Field "%s" is not a mapped field of the entity "%s".', $config->map[$locale],
                    $metadata->getName())
            );
        }

        $localeField = $property.Inflector::classify($locale);

        if ($metadata->hasField($localeField)) {
            $config->map[$locale] = $localeField;

            return $localeField;
        }

        $localeField = $property.Inflector::classify($defaultLocale);

        if ($metadata->hasField($localeField)) {
            $config->map[$locale] = $localeField;

            return $localeField;
        }

        throw new \RuntimeException(
            sprintf('Failed to find a locale field. Entity: "%s", field: "%s", locale: "%s".',
                $metadata->getName(), $property, $locale)
        );
    }

    /**
     * @param ClassMetadata $metadata
     *
     * @return TranslatableFieldConfig[]
     */
    private function getClassConfigs(ClassMetadata $metadata)
    {
        $class = $metadata->getName();

        if (isset($this->configs[$class])) {
            return $this->configs[$class];
        }

        $this->configs[$class] = [];

        foreach ($metadata->getReflectionClass()->getProperties() as $property) {
            /** @var TranslatableField $config */
            $config = $this->reader->getPropertyAnnotation($property, TranslatableField::class);

            if (null === $config) {
                continue;
            }

            $this->configs[$class][$property->getName()] = new TranslatableFieldConfig($config);
        }

        return $this->configs[$class];
    }
}
