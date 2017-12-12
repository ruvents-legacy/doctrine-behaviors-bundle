<?php

namespace Ruvents\DoctrineBundle\Mapping;

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
