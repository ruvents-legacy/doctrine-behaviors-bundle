<?php

declare(strict_types=1);

namespace Ruwork\DoctrineBehaviorsBundle\EventListener;

use App\EventListener\DoctrineListener;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Ruwork\DoctrineBehaviorsBundle\Doctrine\Types\TsvectorType;
use Ruwork\DoctrineBehaviorsBundle\Metadata\MetadataFactoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class SearchIndexListener
{
    private $factory;

    private $accessor;

    public function __construct(MetadataFactoryInterface $factory, PropertyAccessor $accessor = null)
    {
        $this->factory = $factory;
        $this->accessor = $accessor ?? PropertyAccess::createPropertyAccessor();
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $args)
    {
        $platform = $args->getEntityManager()->getConnection()->getDatabasePlatform();

        if ('postgresql' !== $platform->getName()) {
            return;
        }

        $entityMetadata = $args->getClassMetadata();
        $class = $entityMetadata->getName();
        $metadata = $this->factory->getMetadata($class);

        foreach ($metadata->getSearchIndexes() as $property => $searchIndex) {
            if (TsvectorType::NAME !== (string) $entityMetadata->getTypeOfField($property)) {
                continue;
            }

            $column = $entityMetadata->getColumnName($property);
            $indexName = $this->generateIndexName($column);

            $entityMetadata->table['indexes'][$indexName] = [
                'columns' => [
                    $column,
                ],
                'flags' => [
                    DoctrineListener::GIN,
                ],
            ];
        }

        $args->getEntityManager()
            ->getMetadataFactory()
            ->setMetadataFor($class, $entityMetadata);
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        $class = get_class($entity);
        $entityMetadata = $args->getEntityManager()->getClassMetadata($class);
        $metadata = $this->factory->getMetadata($class);

        foreach ($metadata->getSearchIndexes() as $property => $searchIndex) {
            $generator = $this->getGenerator($entity, $searchIndex->propertyPaths);
            $value = $this->implodeRecursive($generator);
            $entityMetadata->setFieldValue($entity, $property, $value);
        }
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $this->prePersist($args);
    }

    private function generateIndexName(string $column): string
    {
        return substr('idx_'.dechex(crc32($column)), 0, 30);
    }

    private function getGenerator($entity, array $propertyPaths): \Generator
    {
        foreach ($propertyPaths as $propertyPath) {
            yield $this->accessor->getValue($entity, $propertyPath);
        }
    }

    private function implodeRecursive($value): string
    {
        if (is_iterable($value)) {
            $string = '';

            foreach ($value as $item) {
                $string .= ' '.$this->implodeRecursive($item);
            }

            return $string;
        }

        return (string) $value;
    }
}
