<?php

namespace Ruvents\DoctrineBundle\EventListener;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Ruvents\DoctrineBundle\Annotation\Translatable;
use Ruvents\DoctrineBundle\Config\TranslatableConfig;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class TranslatableDoctrineListener implements EventSubscriber
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
     * @var PropertyAccessor
     */
    private $accessor;

    /**
     * @var string
     */
    private $currentLocale;

    /**
     * @var TranslatableConfig[][]
     */
    private $configs = [];

    /**
     * @param Reader                $reader
     * @param string                $defaultLocale
     * @param PropertyAccessor|null $accessor
     */
    public function __construct(Reader $reader, $defaultLocale, PropertyAccessor $accessor = null)
    {
        $this->reader = $reader;
        $this->defaultLocale = $defaultLocale;
        $this->accessor = $accessor ?: PropertyAccess::createPropertyAccessor();
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

        foreach ($configs as $property => $config) {
            $localePath = $this->getLocalePath($config->propertyPath, $this->currentLocale);
            $value = $this->getLocaleValue($entity, $localePath);

            if (null === $value && $this->defaultLocale !== $this->currentLocale) {
                $localePath = $this->getLocalePath($config->propertyPath, $this->defaultLocale);
                $value = $this->getLocaleValue($entity, $localePath);
            }

            $reflectionProperty = $metadata->getReflectionClass()->getProperty($property);

            if (!$reflectionProperty->isPublic()) {
                $reflectionProperty->setAccessible(true);
            }

            $reflectionProperty->setValue($entity, $value);
        }
    }

    /**
     * @param ClassMetadata $metadata
     *
     * @return TranslatableConfig[]
     */
    private function getClassConfigs(ClassMetadata $metadata)
    {
        $class = $metadata->getName();

        if (isset($this->configs[$class])) {
            return $this->configs[$class];
        }

        $this->configs[$class] = [];

        foreach ($metadata->getReflectionClass()->getProperties() as $property) {
            /** @var Translatable $config */
            $config = $this->reader->getPropertyAnnotation($property, Translatable::class);

            if (null === $config) {
                continue;
            }

            $this->configs[$class][$property->getName()] = new TranslatableConfig($config);
        }

        return $this->configs[$class];
    }

    /**
     * @param object $entity
     * @param string $path
     *
     * @return mixed|null
     */
    private function getLocaleValue($entity, $path)
    {
        return $this->accessor->isReadable($entity, $path)
            ? $this->accessor->getValue($entity, $path)
            : null;
    }

    /**
     * @param string $propertyPath
     * @param string $locale
     *
     * @return string
     */
    private function getLocalePath($propertyPath, $locale)
    {
        return str_replace(['%locale%', '%Locale%'], [$locale, ucfirst($locale)], $propertyPath);
    }
}
