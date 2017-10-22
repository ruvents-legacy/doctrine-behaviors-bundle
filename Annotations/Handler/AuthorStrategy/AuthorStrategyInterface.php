<?php

namespace Ruvents\DoctrineBundle\Annotations\Handler\AuthorStrategy;

use Ruvents\DoctrineBundle\Annotations\Mapping\Author;

interface AuthorStrategyInterface
{
    /**
     * @param Author $annotation   The annotation mapping object
     * @param string $type         The type of the field
     * @param mixed  $currentValue The current value of the field
     *
     * @return mixed
     */
    public function getFieldAuthor(Author $annotation, string $type, $currentValue);

    /**
     * @param Author $annotation   The annotation mapping object
     * @param string $targetClass  The target class of the association
     * @param mixed  $currentValue The current value of the field
     *
     * @return null|object
     */
    public function getAssociationAuthor(Author $annotation, string $targetClass, $currentValue);
}
