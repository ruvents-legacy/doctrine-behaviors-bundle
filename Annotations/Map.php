<?php

namespace Ruvents\DoctrineBundle\Annotations;

class Map
{
    /**
     * @var object[]
     */
    private $classAnnotations = [];

    /**
     * @var object[][]
     */
    private $propertyAnnotations = [];

    public function addClassAnnotation($annotation)
    {
        $this->classAnnotations[] = $annotation;
    }

    public function addPropertyAnnotation(string $property, $annotation)
    {
        $this->propertyAnnotations[$property][] = $annotation;
    }

    /**
     * @return object[]
     */
    public function getClassAnnotations(): array
    {
        return $this->classAnnotations;
    }

    /**
     * @return object[][]
     */
    public function getPropertyAnnotations(): array
    {
        return $this->propertyAnnotations;
    }
}
