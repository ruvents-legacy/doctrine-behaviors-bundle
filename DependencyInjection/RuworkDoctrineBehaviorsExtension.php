<?php

declare(strict_types=1);

namespace Ruwork\DoctrineBehaviorsBundle\DependencyInjection;

use Ruwork\DoctrineBehaviorsBundle\Metadata\LazyLoadingMetadataFactory;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
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

        if (isset($byConnection[Configuration::CONNECTION_ANY]) && 1 === count($byConnection)) {
            $this->configureConnection($container, $byConnection[Configuration::CONNECTION_ANY]);
        } else {
            foreach ($config['by_connection'] as $connection => $connectionConfig) {
                $this->configureConnection($container, $connectionConfig, $connection);
            }
        }
    }

    private function configureConnection(ContainerBuilder $container, array $config, string $connection = null): void
    {
        if (null === $connection) {
            $tagAttributes = [];
            $connection = 'any';
        } else {
            $tagAttributes = ['connection' => $connection];
        }

        foreach ($config as $behavior => $behaviorConfig) {
            if (!$behaviorConfig['enabled']) {
                continue;
            }

            $definition = (new ChildDefinition(self::LISTENER.$behavior))
                ->addTag('doctrine.event_subscriber', $tagAttributes);

            switch ($behavior) {
                case 'author':
                    $this->configureAuthorListener($definition, $behaviorConfig);
                    break;

                case 'multilingual':
                    $this->configureMultilingualListener($definition, $behaviorConfig);
                    break;

                case 'persist_timestamp':
                case 'update_timestamp':
                    $this->configureTimestampListener($definition, $behaviorConfig);
                    break;
            }

            $container->setDefinition(self::LISTENER.$behavior.'.'.$connection, $definition);
        }
    }

    private function configureAuthorListener(Definition $definition, array $config): void
    {
        $definition->setArgument('$strategy', new Reference($config['strategy']));
    }

    private function configureMultilingualListener(Definition $definition, array $config): void
    {
        $definition
            ->setArgument('$defaultLocale', $config['default_locale'])
            ->addTag('kernel.event_listener', ['event' => KernelEvents::REQUEST]);
    }

    private function configureTimestampListener(Definition $definition, array $config): void
    {
        $definition->setArgument('$strategy', new Reference($config['strategy']));
    }
}
