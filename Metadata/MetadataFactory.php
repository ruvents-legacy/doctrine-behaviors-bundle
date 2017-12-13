<?php

declare(strict_types=1);

namespace Ruvents\DoctrineBundle\Metadata;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Ruvents\DoctrineBundle\Mapping\Author;
use Ruvents\DoctrineBundle\Mapping\PersistTimestamp;
use Ruvents\DoctrineBundle\Mapping\SearchIndex;
use Ruvents\DoctrineBundle\Mapping\Translatable;
use Ruvents\DoctrineBundle\Mapping\UpdateTimestamp;

class MetadataFactory implements MetadataFactoryInterface
{
    private $doctrine;

    private $annotationReader;

    public function __construct(ManagerRegistry $doctrine, Reader $annotationReader)
    {
        $this->doctrine = $doctrine;
        $this->annotationReader = $annotationReader;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata(string $class): Metadata
    {
        $manager = $this->doctrine->getManagerForClass($class);

        if (!$manager instanceof EntityManagerInterface) {
            throw new \InvalidArgumentException(sprintf('Class "%s" is not a doctrine entity.'));
        }

        $entityMetadata = $manager->getClassMetadata($class);
        $reflectionClass = new \ReflectionClass($class);
        $metadata = new Metadata($class);

        foreach ($entityMetadata->getFieldNames() as $property) {
            if (!$reflectionClass->hasProperty($property)) {
                continue;
            }

            $reflectionProperty = $reflectionClass->getProperty($property);

            foreach ($this->annotationReader->getPropertyAnnotations($reflectionProperty) as $annotation) {
                if ($annotation instanceof Author) {
                    $metadata->addAuthor($property, $annotation);
                } elseif ($annotation instanceof PersistTimestamp) {
                    $metadata->addPersistTimestamp($property, $annotation);
                } elseif ($annotation instanceof SearchIndex) {
                    $metadata->addSearchIndex($property, $annotation);
                } elseif ($annotation instanceof UpdateTimestamp) {
                    $metadata->addUpdateTimestamp($property, $annotation);
                }
            }
        }

        foreach ($entityMetadata->getAssociationNames() as $property) {
            if (!$reflectionClass->hasProperty($property)) {
                continue;
            }

            $reflectionProperty = $reflectionClass->getProperty($property);

            foreach ($this->annotationReader->getPropertyAnnotations($reflectionProperty) as $annotation) {
                if ($annotation instanceof Author) {
                    $metadata->addAuthor($property, $annotation);
                }
            }
        }

        foreach ($entityMetadata->embeddedClasses as $property => $mapping) {
            if (!$reflectionClass->hasProperty($property)) {
                continue;
            }

            $reflectionProperty = $reflectionClass->getProperty($property);

            foreach ($this->annotationReader->getPropertyAnnotations($reflectionProperty) as $annotation) {
                if ($annotation instanceof Translatable) {
                    $metadata->addTranslatable($property, $annotation);
                }
            }
        }

        return $metadata;
    }
}
