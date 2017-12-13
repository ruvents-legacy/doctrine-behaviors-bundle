<?php

declare(strict_types=1);

namespace Ruvents\DoctrineBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Ruvents\DoctrineBundle\Metadata\MetadataFactoryInterface;

class SearchIndexListener
{
    private $factory;

    public function __construct(MetadataFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        $class = get_class($entity);
        $entityMetadata = $args->getEntityManager()->getClassMetadata($class);

        foreach ($this->factory->getMetadata($class)->getSearchIndexes() as $property => $searchIndex) {
            $method = $searchIndex->generatorMethod ?? 'generate'.ucfirst($property);
            $generator = $entity->$method();

            if (!$generator instanceof \Generator) {
                throw new \UnexpectedValueException(sprintf('Method "%s::%s()" must return a \\Generator.', $class, $method));
            }

            $entityMetadata->setFieldValue($entity, $property, $this->createIndexString($generator));
        }
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $this->prePersist($args);
    }

    private function createIndexString(\Generator $generator): string
    {
        $value = '';

        foreach ($generator as $phrase) {
            $value .= (string) $phrase.' ';
        }

        return trim(preg_replace('/\s+/', ' ', $value));
    }
}
