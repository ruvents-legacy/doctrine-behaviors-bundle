<?php

namespace Ruvents\DoctrineBundle\Mapping\Metadata;

use Ruvents\DoctrineBundle\Mapping\Author;

trait AuthorMetadataTrait
{
    /**
     * @var Author[]
     */
    public $authorProperties = [];

    /**
     * @see AuthorMetadataInterface::addAuthorProperty
     */
    public function addAuthorProperty($property, Author $author)
    {
        $this->authorProperties[$property] = $author;
    }

    /**
     * @see AuthorMetadataInterface::getAuthorProperties
     */
    public function getAuthorProperties()
    {
        return $this->authorProperties;
    }

    /**
     * @see AuthorMetadataInterface::isAuthorProperty
     */
    public function isAuthorProperty($property)
    {
        return isset($this->authorProperties[$property]);
    }
}
