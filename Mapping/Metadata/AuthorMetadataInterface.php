<?php

namespace Ruvents\DoctrineBundle\Mapping\Metadata;

use Ruvents\DoctrineBundle\Mapping\Author;

interface AuthorMetadataInterface extends ClassMetadataInterface
{
    /**
     * @param string $property
     * @param Author $mapping
     */
    public function addAuthorMapping($property, Author $mapping);

    /**
     * @return Author[]
     */
    public function getAuthorMappings();

    /**
     * @param string $property
     *
     * @return bool
     */
    public function hasAuthorMapping($property);
}
