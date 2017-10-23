<?php

namespace Ruvents\DoctrineBundle\Annotations\Handler;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Ruvents\DoctrineBundle\Annotations\Map;
use Ruvents\DoctrineBundle\Annotations\Mapping\Translatable;
use Ruvents\DoctrineBundle\Translations\TranslationsInterface;
use Ruvents\DoctrineBundle\Translations\TranslationsManager;
use Symfony\Component\HttpFoundation\RequestStack;

class TranslatableHandler implements HandlerInterface
{
    /**
     * @var TranslationsManager
     */
    private $manager;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(TranslationsManager $manager, RequestStack $requestStack)
    {
        $this->manager = $manager;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public static function supportsAnnotation($annotation, int $target): bool
    {
        return Target::TARGET_PROPERTY === $target && $annotation instanceof Translatable;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Events::loadClassMetadata,
            Events::prePersist,
            Events::postLoad,
        ];
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $args, Map $map)
    {
        $metadata = $args->getClassMetadata();

        foreach ($map->getPropertyAnnotations() as $property => $annotations) {
            if (!isset($metadata->embeddedClasses[$property]['class'])) {
                throw new \LogicException('Translatable field must have an @Embedded mapping.');
            }

            $this->validateTranslationsClass($metadata->embeddedClasses[$property]['class']);
        }
    }

    public function prePersist(LifecycleEventArgs $args, Map $map)
    {
        $entity = $args->getEntity();
        $metadata = $args->getEntityManager()->getClassMetadata(get_class($entity));

        foreach ($map->getPropertyAnnotations() as $property => $annotations) {
            $value = $metadata->getFieldValue($entity, $property);

            if (null === $value) {
                $class = $metadata->embeddedClasses[$property]['class'];
                $metadata->setFieldValue($entity, $property, $value = new $class);
            } elseif (!$value instanceof TranslationsInterface) {
                throw new \LogicException(
                    sprintf('Translatable @Embeddable must be an instance of %s.', TranslationsInterface::class)
                );
            }

            $this->manager->register($value);
        }
    }

    public function postLoad(LifecycleEventArgs $args, Map $map)
    {
        $entity = $args->getEntity();
        $metadata = $args->getEntityManager()->getClassMetadata(get_class($entity));

        foreach ($map->getPropertyAnnotations() as $property => $annotations) {
            $this->manager->register($metadata->getFieldValue($entity, $property));
        }
    }

    private function validateTranslationsClass($class)
    {
        if (!is_subclass_of($class, TranslationsInterface::class)) {
            throw new \LogicException(
                sprintf('Translatable @Embeddable must be an instance of %s.', TranslationsInterface::class)
            );
        }

        $locales = call_user_func([$class, $method = 'getLocalesMap']);

        if ([] === $locales) {
            throw new \LogicException(
                sprintf('The %s::%s() method must return at least one value.', $class, $method)
            );
        }

        foreach ($locales as $locale => $nb) {
            if (!property_exists($class, $locale)) {
                throw new \LogicException(
                    sprintf('Property "%s" is not defined in %s.', $locale, get_class($this))
                );
            }
        }
    }
}
