<?php

declare(strict_types=1);

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
