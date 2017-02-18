<?php

namespace Ruvents\DoctrineBundle\Mapping\Metadata;

use Ruvents\DoctrineBundle\Mapping\Author;

interface AuthorMetadataInterface extends ClassMetadataInterface
{
    /**
     * @param string $property
     * @param Author $author
     */
    public function addAuthorProperty($property, Author $author);

    /**
     * @return Author[]
     */
    public function getAuthorProperties();

    /**
     * @param string $property
     *
     * @return bool
     */
    public function isAuthorProperty($property);
}
