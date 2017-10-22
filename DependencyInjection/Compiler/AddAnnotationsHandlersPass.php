<?php

namespace Ruvents\DoctrineBundle\DependencyInjection\Compiler;

use Ruvents\DoctrineBundle\Annotations\EventListener;
use Ruvents\DoctrineBundle\Annotations\Handler\HandlerInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Reference;

class AddAnnotationsHandlersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(EventListener::class)) {
            return;
        }

        $handlerServices = $container->findTaggedServiceIds($tag = 'ruwork_doctrine.annotations_handler', true);

        $handlers = [];

        foreach ($handlerServices as $id => $attributes) {
            $class = $container->getDefinition($id)->getClass();

            if (!is_subclass_of($class,HandlerInterface::class)) {
                throw new InvalidArgumentException(sprintf('Services tagged with "%s" must implement %s.', $tag, HandlerInterface::class));
            }

            $handlers[$container->getDefinition($id)->getClass()] = new Reference($id);
        }

        $container
            ->getDefinition(EventListener::class)
            ->setArgument('$handlerClasses', array_keys($handlers))
            ->setArgument('$container', ServiceLocatorTagPass::register($container, $handlers));
    }
}
