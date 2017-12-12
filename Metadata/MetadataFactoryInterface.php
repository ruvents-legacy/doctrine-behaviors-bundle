<?php

declare(strict_types=1);

namespace Ruvents\DoctrineBundle\Metadata;

interface MetadataFactoryInterface
{
    public function getMetadata(string $class): Metadata;
}
