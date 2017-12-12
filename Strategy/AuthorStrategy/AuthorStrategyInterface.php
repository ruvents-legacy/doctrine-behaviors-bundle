<?php

declare(strict_types=1);

namespace Ruvents\DoctrineBundle\Strategy\AuthorStrategy;

use Doctrine\ORM\Mapping\ClassMetadata;

interface AuthorStrategyInterface
{
    /**
     * @return null|string|object
     */
    public function getAuthor(ClassMetadata $metadata, string $property);
}
