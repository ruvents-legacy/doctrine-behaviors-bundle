<?php

declare(strict_types=1);

namespace Ruwork\DoctrineBehaviorsBundle\Metadata;

use Doctrine\Common\Annotations\Reader;
use Ruwork\DoctrineBehaviorsBundle\Mapping\Author;
use Ruwork\DoctrineBehaviorsBundle\Mapping\PersistTimestamp;
use Ruwork\DoctrineBehaviorsBundle\Mapping\SearchIndex;
use Ruwork\DoctrineBehaviorsBundle\Mapping\Translatable;
use Ruwork\DoctrineBehaviorsBundle\Mapping\UpdateTimestamp;

class MetadataFactory implements MetadataFactoryInterface
{
    private $annotationReader;

    public function __construct(Reader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata(string $class): Metadata
    {
        $reflectionClass = new \ReflectionClass($class);
        $metadata = new Metadata($class);

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $name = $reflectionProperty->getName();

            foreach ($this->annotationReader->getPropertyAnnotations($reflectionProperty) as $annotation) {
                if ($annotation instanceof Author) {
                    $metadata->addAuthor($name, $annotation);
                } elseif ($annotation instanceof PersistTimestamp) {
                    $metadata->addPersistTimestamp($name, $annotation);
                } elseif ($annotation instanceof SearchIndex) {
                    $metadata->addSearchIndex($name, $annotation);
                } elseif ($annotation instanceof Translatable) {
                    $metadata->addTranslatable($name, $annotation);
                } elseif ($annotation instanceof UpdateTimestamp) {
                    $metadata->addUpdateTimestamp($name, $annotation);
                }
            }
        }

        return $metadata;
    }
}
