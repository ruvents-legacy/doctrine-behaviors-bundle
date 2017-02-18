<?php

namespace Ruvents\DoctrineBundle\Mapping\Loader;

use Doctrine\Common\Annotations\Reader;
use Ruvents\DoctrineBundle\Mapping\Metadata\ClassMetadataInterface;
use Ruvents\DoctrineBundle\Mapping\Metadata\TranslatableMetadataInterface;
use Ruvents\DoctrineBundle\Mapping\Translatable;

class AnnotationLoader implements LoaderInterface
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
     */
    public function loadClassMetadata(ClassMetadataInterface $metadata)
    {
        foreach ($metadata->getReflectionClass()->getProperties() as $property) {
            foreach ($this->reader->getPropertyAnnotations($property) as $annotation) {
                if ($annotation instanceof Translatable) {
                    if ($metadata instanceof TranslatableMetadataInterface) {
                        $metadata->addTranslatableConfig($property->getName(), $annotation);
                    }
                }
            }
        }
    }
}
