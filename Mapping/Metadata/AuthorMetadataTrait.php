<?php

namespace Ruvents\DoctrineBundle\Mapping\Metadata;

use Ruvents\DoctrineBundle\Mapping\Author;

trait AuthorMetadataTrait
{
    /**
     * @var Author[]
     */
    public $authorMappings = [];

    /**
     * @see AuthorMetadataInterface::addAuthorMapping
     */
    public function addAuthorMapping($property, Author $mapping)
    {
        $this->authorMappings[$property] = $mapping;
    }

    /**
     * @see AuthorMetadataInterface::getAuthorMappings
     */
    public function getAuthorMappings()
    {
        return $this->authorMappings;
    }

    /**
     * @see AuthorMetadataInterface::hasAuthorMapping
     */
    public function hasAuthorMapping($property)
    {
        return isset($this->authorMappings[$property]);
    }
}
