<?php

declare(strict_types=1);

namespace Ruwork\DoctrineBehaviorsBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Ruwork\DoctrineBehaviorsBundle\Metadata\MetadataFactoryInterface;
use Ruwork\DoctrineBehaviorsBundle\Translations\TranslationsInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class TranslatableListener
{
    private $factory;
    private $requestStack;
    private $defaultLocale;

    /**
     * @var \SplObjectStorage|TranslationsInterface[]
     */
    private $storage;

    public function __construct(MetadataFactoryInterface $factory, RequestStack $requestStack, string $defaultLocale)
    {
        $this->factory = $factory;
        $this->requestStack = $requestStack;
        $this->defaultLocale = $defaultLocale;
        $this->storage = new \SplObjectStorage();
    }

    public function onKernelRequest(): void
    {
        $currentLocale = $this->getCurrentLocale();

        foreach ($this->storage as $translations) {
            $translations->setCurrentLocale($currentLocale);
        }
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        $class = get_class($entity);
        $entityMetadata = $args->getEntityManager()->getClassMetadata($class);

        foreach ($this->factory->getMetadata($class)->getTranslatables() as $property => $translatable) {
            $value = $entityMetadata->getFieldValue($entity, $property);

            if (!$value instanceof TranslationsInterface) {
                throw new \UnexpectedValueException(sprintf('Value of %s.%s@Translatable must be an instance of "%s", "%s" given.', $class, $property, TranslationsInterface::class, is_object($value) ? get_class($value) : gettype($value)));
            }

            if (!$this->storage->contains($value)) {
                $value->setCurrentLocale($this->getCurrentLocale());
                $this->storage->attach($value);
            }
        }
    }

    public function postLoad(LifecycleEventArgs $args): void
    {
        $this->prePersist($args);
    }

    private function getCurrentLocale(): string
    {
        if (null !== $request = $this->requestStack->getCurrentRequest()) {
            return $request->getLocale();
        }

        return $this->defaultLocale;
    }
}
