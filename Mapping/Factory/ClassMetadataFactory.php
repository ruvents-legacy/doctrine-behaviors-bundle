<?php

namespace Ruvents\DoctrineBundle\Mapping\Factory;

use Doctrine\Common\Util\ClassUtils;
use Ruvents\DoctrineBundle\Mapping\Loader\LoaderInterface;
use Ruvents\DoctrineBundle\Mapping\Metadata\ClassMetadata;
use Ruvents\DoctrineBundle\Mapping\Metadata\ClassMetadataInterface;

class ClassMetadataFactory implements ClassMetadataFactoryInterface
{
    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * @var ClassMetadataInterface[]
     */
    private $loadedMetadata = [];

    /**
     * @param LoaderInterface $loader
     */
    public function __construct(LoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadataFor($class)
    {
        $class = $this->getClass($class);

        if (isset($this->loadedMetadata[$class])) {
            return $this->loadedMetadata[$class];
        }

        $metadata = new ClassMetadata($class);

        $this->loader->loadClassMetadata($metadata);

        return $this->loadedMetadata[$class] = $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function hasMetadataFor($value)
    {
        try {
            $this->getClass($value);

            return true;
        } catch (\InvalidArgumentException $exception) {
        }

        return false;
    }

    /**
     * @param string|object $value
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    private function getClass($value)
    {
        if (is_string($value)) {
            if (!class_exists($value)) {
                throw new \InvalidArgumentException(
                    sprintf('The class "%s" does not exist.', $value)
                );
            }

            return ltrim($value, '\\');
        }

        if (!is_object($value)) {
            throw new \InvalidArgumentException(
                sprintf('Cannot create metadata for non-objects. Got: "%s"', gettype($value))
            );
        }

        return ClassUtils::getClass($value);
    }
}
