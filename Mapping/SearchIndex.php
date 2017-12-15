<?php

declare(strict_types=1);

namespace Ruvents\DoctrineBundle\Mapping;

/**
 * @Annotation()
 * @Target("PROPERTY")
 */
final class SearchIndex
{
    /**
     * @Required()
     *
     * @var <string>
     */
    public $propertyPaths = [];

    public function setValue($value)
    {
        $this->propertyPaths = $value;
    }
}
