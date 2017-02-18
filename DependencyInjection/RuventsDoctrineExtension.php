<?php

namespace Ruvents\DoctrineBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class RuventsDoctrineExtension extends ConfigurableExtension
{
    /**
     * {@inheritdoc}
     */
    public function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $loader->load('common.yml');

        if ($mergedConfig['timestamp']['enabled']) {
            $loader->load('timestamp.yml');
        }

        if ($mergedConfig['author']['enabled']) {
            $loader->load('author.yml');

            $container->findDefinition('ruvents_doctrine.doctrine.event_listener.author')
                ->replaceArgument(1, new Reference($mergedConfig['author']['provider_id']));
        }

        if ($mergedConfig['translatable']['enabled']) {
            $loader->load('translatable.yml');

            $container->findDefinition('ruvents_doctrine.translator')
                ->replaceArgument(1, $mergedConfig['translatable']['fallbacks']);
        }
    }
}
