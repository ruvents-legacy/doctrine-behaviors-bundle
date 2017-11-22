<?php

namespace Ruvents\DoctrineBundle\Annotations;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Psr\Container\ContainerInterface;

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

        if ($metadata->isMappedSuperclass || $metadata->isEmbeddedClass) {
            return;
        }

        $name = $metadata->getName();
        $reflectionClass = $metadata->getReflectionClass();

        foreach ($this->reader->getClassAnnotations($reflectionClass) as $annotation) {
            foreach ($this->handlerClasses as $handler) {
                if (call_user_func([$handler, 'supportsAnnotation'], $annotation, Target::TARGET_CLASS)) {
                    $this->getMap($handler, $name)->addClassAnnotation($annotation);

                    continue(2);
                }
            }
        }

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $property = $reflectionProperty->getName();

            foreach ($this->reader->getPropertyAnnotations($reflectionProperty) as $annotation) {
                foreach ($this->handlerClasses as $handler) {
                    if (call_user_func([$handler, 'supportsAnnotation'], $annotation, Target::TARGET_PROPERTY)) {
                        $this->getMap($handler, $name)->addPropertyAnnotation($property, $annotation);

                        continue(2);
                    }
                }
            }
        }

        foreach ($this->events[Events::loadClassMetadata] ?? [] as $handler) {
            $this->callHandlerListener(Events::loadClassMetadata, $handler, $args, $name);
        }

        $args->getEntityManager()
            ->getEventManager()
            ->addEventListener(array_keys($this->events), $this);
    }

    public function __call($event, $arguments)
    {
        if (!isset($this->events[$event])) {
            throw new \BadMethodCallException(sprintf('Event "%s" is not registered.', $event));
        }

        $args = $arguments[0];
        $entity = $args instanceof LifecycleEventArgs ? ClassUtils::getClass($args->getEntity()) : null;

        foreach ($this->events[$event] as $handler) {
            $this->callHandlerListener($event, $handler, $args, $entity);
        }
    }

    private function callHandlerListener($event, $handler, $args, $entity = null)
    {
        if (null === $entity) {
            call_user_func([$this->container->get($handler), $event], $args, $this->maps[$handler]);

            return;
        }

        if (isset($this->maps[$handler][$entity])) {
            call_user_func([$this->container->get($handler), $event], $args, $this->maps[$handler][$entity]);
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
