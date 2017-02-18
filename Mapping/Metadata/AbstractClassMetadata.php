<?php

namespace Ruvents\DoctrineBundle\Mapping\Metadata;

abstract class AbstractClassMetadata implements ClassMetadataInterface
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var \ReflectionClass
     */
    private $reflectionClass;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getReflectionClass()
    {
        if (!isset($this->reflectionClass)) {
            $this->reflectionClass = new \ReflectionClass($this->name);
        }

        return $this->reflectionClass;
    }
}
