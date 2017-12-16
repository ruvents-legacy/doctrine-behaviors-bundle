<?php

declare(strict_types=1);

namespace Ruwork\DoctrineBehaviorsBundle\DependencyInjection;

use Doctrine\ORM\Events;
use Ruwork\DoctrineBehaviorsBundle\Metadata\LazyLoadingMetadataFactory;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\HttpKernel\KernelEvents;

class RuworkDoctrineBehaviorsExtension extends ConfigurableExtension
{
    const PREFIX = 'ruwork_doctrine_behaviors.';
    const LISTENER = self::PREFIX.'listener.';

    /**
     * {@inheritdoc}
     */
    public function loadInternal(array $config, ContainerBuilder $container): void
    {
        (new PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config')))
            ->load('services.php');

        if ($container->getParameter('kernel.debug')) {
            $container->findDefinition(LazyLoadingMetadataFactory::class)
                ->setArgument('$cache', null);
        }

        $byConnection = $config['by_connection'];
        $anyConnection = isset($byConnection[Configuration::CONNECTION_ANY]) && 1 === count($byConnection);

        foreach ($byConnection as $connection => $connectionConfig) {
            $listeners = $this->getListeners($connectionConfig, $anyConnection ? null : $connection);

            foreach ($listeners as $listener) {
                $id = $listener->getParent().'.'.$connection;
                $container->setDefinition($id, $listener);
            }
        }
    }

    /**
     * @return \Generator|ChildDefinition[]
     */
    private function getListeners(array $config, string $connection = null): \Generator
    {
        $tagAttr = ['lazy' => true] + ($connection ? ['connection' => $connection] : []);

        foreach ($config as $behavior => $behaviorConfig) {
            if (!$behaviorConfig['enabled']) {
                continue;
            }

            $definition = new ChildDefinition(self::LISTENER.$behavior);

            switch ($behavior) {
                case 'author':
                    $definition
                        ->setArgument('$strategy', new Reference($behaviorConfig['strategy']))
                        ->addTag('doctrine.event_listener', $tagAttr + ['event' => Events::prePersist]);
                    break;

                case 'multilingual':
                    $definition
                        ->setArgument('$defaultLocale', $behaviorConfig['default_locale'])
                        ->addTag('kernel.event_listener', ['event' => KernelEvents::REQUEST])
                        ->addTag('doctrine.event_listener', $tagAttr + ['event' => Events::prePersist])
                        ->addTag('doctrine.event_listener', $tagAttr + ['event' => Events::postLoad]);
                    break;

                case 'persist_timestamp':
                    $definition
                        ->setArgument('$strategy', new Reference($behaviorConfig['strategy']))
                        ->addTag('doctrine.event_listener', $tagAttr + ['event' => Events::prePersist]);
                    break;

                case 'update_timestamp':
                    $definition
                        ->setArgument('$strategy', new Reference($behaviorConfig['strategy']))
                        ->addTag('doctrine.event_listener', $tagAttr + ['event' => Events::preUpdate]);
                    break;
            }

            if ($behaviorConfig['default_mapping']['enabled']) {
                $variant = $behaviorConfig['default_mapping']['enabled_variant'];
                $mapping = $behaviorConfig['default_mapping'][$variant];

                $definition
                    ->addMethodCall('setDefaultMapping', [$variant, $mapping])
                    ->addTag('doctrine.event_listener', $tagAttr + ['event' => Events::loadClassMetadata]);
            }

            yield $definition;
        }
    }
}
