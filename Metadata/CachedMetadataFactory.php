<?php

declare(strict_types=1);

namespace Ruvents\DoctrineBundle\Metadata;

use Psr\Cache\CacheItemPoolInterface;
use Doctrine\Common\Util\ClassUtils;

class CachedMetadataFactory implements MetadataFactoryInterface
{
    private const PREFIX = 'ruvents_doctrine_bundle.metadata.';

    private $factory;

    private $cache;

    private $debug;

    public function __construct(MetadataFactoryInterface $factory, CacheItemPoolInterface $cache, bool $debug = false)
    {
        $this->factory = $factory;
        $this->cache = $cache;
        $this->debug = $debug;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata(string $class): Metadata
    {
        $class = ClassUtils::getRealClass($class);
        $item = $this->cache->getItem($this->getKey($class));

        if ($item->isHit()) {
            /* @var Metadata $metadata */
            [$metadata, $timestamp] = $item->get();

            if (!$this->debug || $timestamp === $classMTime = $this->getClassMTime($class)) {
                return $metadata;
            }
        }

        $metadata = $this->factory->getMetadata($class);
        $classMTime = $classMTime ?? $this->getClassMTime($class);

        $item->set([$metadata, $classMTime]);
        $this->cache->save($item);

        return $metadata;
    }

    private function getKey(string $class): string
    {
        return sha1(self::PREFIX.$class);
    }

    private function getClassMTime(string $class): int
    {
        return filemtime((new \ReflectionClass($class))->getFileName());
    }
}
