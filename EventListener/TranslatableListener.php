<?php

declare(strict_types=1);

namespace Ruvents\DoctrineBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Ruvents\DoctrineBundle\Metadata\MetadataFactoryInterface;
use Ruvents\DoctrineBundle\Translations\TranslationsInterface;
use Ruvents\DoctrineBundle\Translations\TranslationsManager;

class TranslatableListener implements EventSubscriber
{
    private $factory;
    private $manager;

    public function __construct(MetadataFactoryInterface $factory, TranslationsManager $manager)
    {
        $this->factory = $factory;
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::postLoad,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        $class = get_class($entity);
        $entityMetadata = $args->getEntityManager()->getClassMetadata($class);

        foreach ($this->factory->getMetadata($class)->getTranslatables() as $property => $translatable) {
            $value = $entityMetadata->getFieldValue($entity, $property);

            if (null === $value) {
                $translationsClass = $entityMetadata->embeddedClasses[$property]['class'];

                try {
                    $value = new $translationsClass;
                    $entityMetadata->setFieldValue($entity, $property, $value);
                } catch (\Throwable $exception) {
                    throw new \UnexpectedValueException(sprintf('Failed to instantiate class "%s" for %s.%s@Translatable. Please, instantiate it manually in the constructor.', $translationsClass, $class, $property), 0, $exception);
                }
            }

            if (!$value instanceof TranslationsInterface) {
                throw new \UnexpectedValueException(sprintf('Value of %s.%s@Translatable must be an instance of "%s", "%s" given.', $class, $property, TranslationsInterface::class, is_object($value) ? get_class($value) : gettype($value)));
            }

            $this->manager->register($value);
        }
    }

    public function postLoad(LifecycleEventArgs $args): void
    {
        $this->prePersist($args);
    }
}
