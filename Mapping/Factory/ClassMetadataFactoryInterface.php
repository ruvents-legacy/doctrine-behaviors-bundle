<?php

namespace Ruvents\DoctrineBundle\Mapping\Factory;

use Ruvents\DoctrineBundle\Mapping\Metadata\ClassMetadataInterface;

interface ClassMetadataFactoryInterface
{
    /**
     * @param string|object $value
     *
     * @return ClassMetadataInterface
     */
    public function getMetadataFor($value);

    /**
     * @param string|object $value
     *
     * @return bool
     */
    public function hasMetadataFor($value);
}
