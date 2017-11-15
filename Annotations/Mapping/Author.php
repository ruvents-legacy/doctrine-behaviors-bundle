<?php

namespace Ruvents\DoctrineBundle\Annotations\Mapping;

/**
 * @Annotation()
 * @Target("PROPERTY")
 */
final class Author
{
    /**
     * @var bool
     */
    public $overwrite = true;
}
