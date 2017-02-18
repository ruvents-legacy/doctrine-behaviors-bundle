<?php

namespace Ruvents\DoctrineBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
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

        if ($mergedConfig['translatable']['enabled']) {
            $loader->load('translatable.yml');

            $container->findDefinition('ruvents_doctrine.translator')
                ->replaceArgument(1, $mergedConfig['translatable']['fallbacks']);
        }
    }
}
