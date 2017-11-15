<?php

namespace Ruvents\DoctrineBundle\Annotations\Mapping;

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
