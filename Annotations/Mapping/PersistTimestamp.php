<?php

namespace Ruvents\DoctrineBundle\Annotations\Mapping;

/**
 * @Annotation()
 * @Target("PROPERTY")
 */
final class PersistTimestamp
{
    /**
     * @var bool
     */
    public $overwrite = true;
}
