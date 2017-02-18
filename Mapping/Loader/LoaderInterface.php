<?php

namespace Ruvents\DoctrineBundle\Mapping\Loader;

use Ruvents\DoctrineBundle\Mapping\Metadata\ClassMetadataInterface;

interface LoaderInterface
{
    /**
     * @param ClassMetadataInterface $metadata
     */
    public function loadClassMetadata(ClassMetadataInterface $metadata);
}
