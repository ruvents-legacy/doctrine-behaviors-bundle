<?php

namespace Ruvents\DoctrineBundle\Annotations;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Psr\Container\ContainerInterface;
use Ruvents\DoctrineBundle\Annotations\Handler\HandlerInterface;

class EventListener implements EventSubscriber
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var array
     */
    private $handlerClasses;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var Map[][]
     */
    private $maps = [];

    /**
     * @var string[][]
     */
    private $events = [];

    public function __construct(Reader $reader, array $handlerClasses, ContainerInterface $container)
    {
        $this->reader = $reader;
        $this->handlerClasses = $handlerClasses;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::loadClassMetadata,
        ];
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $args)
    {
        $metadata = $args->getClassMetadata();

        if ($metadata->isMappedSuperclass) {
            return;
        }

        $name = $metadata->getName();
        $reflectionClass = $metadata->getReflectionClass();

        foreach ($this->reader->getClassAnnotations($reflectionClass) as $annotation) {
            foreach ($this->handlerClasses as $handler) {
                if (call_user_func([$handler, 'supportsAnnotation'], $annotation, HandlerInterface::TYPE_CLASS)) {
                    $this->getMap($handler, $name)->addClassAnnotation($annotation);

                    continue(2);
                }
            }
        }

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $property = $reflectionProperty->getName();

            foreach ($this->reader->getPropertyAnnotations($reflectionProperty) as $annotation) {
                foreach ($this->handlerClasses as $handler) {
                    if (call_user_func([$handler, 'supportsAnnotation'], $annotation, HandlerInterface::TYPE_PROPERTY)) {
                        $this->getMap($handler, $name)->addPropertyAnnotation($property, $annotation);

                        continue(2);
                    }
                }
            }
        }

        if (isset($this->events[Events::loadClassMetadata])) {
            foreach ($this->events[Events::loadClassMetadata] as $handler => $nb) {
                call_user_func([$this->container->get($handler), Events::loadClassMetadata],
                    $args, $this->maps[$handler][$name]);
            }
        }

        $args->getEntityManager()
            ->getEventManager()
            ->addEventListener(array_keys($this->events), $this);
    }

    public function __call($name, $arguments)
    {
        $args = $arguments[0];

        if ($args instanceof LifecycleEventArgs) {
            $entity = get_class($args->getEntity());
        }

        foreach ($this->events[$name] as $handler) {
            call_user_func([$this->container->get($handler), $name],
                $args, isset($entity) ? $this->maps[$handler][$entity] : $this->maps[$handler]);
        }
    }

    private function getMap(string $handler, string $entity): Map
    {
        if (!isset($this->maps[$handler][$entity])) {
            if (!isset($this->maps[$handler])) {
                foreach (call_user_func([$handler, 'getSubscribedEvents']) as $event) {
                    $this->events[$event][] = $handler;
                }
            }

            $this->maps[$handler][$entity] = new Map();
        }

        return $this->maps[$handler][$entity];
    }
}
