<?php

namespace Ruvents\DoctrineBundle\Mapping\Metadata;

interface ClassMetadataInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return \ReflectionClass
     */
    public function getReflectionClass();
}
