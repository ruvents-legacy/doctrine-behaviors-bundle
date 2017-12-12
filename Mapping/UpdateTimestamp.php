<?php

namespace Ruvents\DoctrineBundle\Mapping;

/**
 * @Annotation()
 * @Target("PROPERTY")
 */
final class UpdateTimestamp
{
    /**
     * @var bool
     */
    public $overwrite = true;
}
