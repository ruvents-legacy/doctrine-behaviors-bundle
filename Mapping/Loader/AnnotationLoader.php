<?php

namespace Ruvents\DoctrineBundle\Mapping\Loader;

use Doctrine\Common\Annotations\Reader;
use Ruvents\DoctrineBundle\Mapping;
use Ruvents\DoctrineBundle\Mapping\Metadata;

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
    public function loadClassMetadata(Metadata\ClassMetadataInterface $metadata)
    {
        foreach ($metadata->getReflectionClass()->getProperties() as $property) {
            foreach ($this->reader->getPropertyAnnotations($property) as $annotation) {
                if ($annotation instanceof Mapping\Timestamp) {
                    if ($metadata instanceof Metadata\TimestampMetadataInterface) {
                        $metadata->addTimestampMapping($property->getName(), $annotation);
                    }
                }

                if ($annotation instanceof Mapping\Author) {
                    if ($metadata instanceof Metadata\AuthorMetadataInterface) {
                        $metadata->addAuthorMapping($property->getName(), $annotation);
                    }
                }

                if ($annotation instanceof Mapping\Translatable) {
                    if ($metadata instanceof Metadata\TranslatableMetadataInterface) {
                        $metadata->addTranslatableMapping($property->getName(), $annotation);
                    }
                }

                if ($annotation instanceof Mapping\UseDate) {
                    if ($metadata instanceof Metadata\UseDateMetadataInterface) {
                        $metadata->addUseDateMapping($property->getName(), $annotation);
                    }
                }
            }
        }
    }
}
