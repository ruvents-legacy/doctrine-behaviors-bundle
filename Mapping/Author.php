<?php

namespace Ruvents\DoctrineBundle\Mapping;

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
