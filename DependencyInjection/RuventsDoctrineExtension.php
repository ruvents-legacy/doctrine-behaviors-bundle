<?php

namespace Ruvents\DoctrineBundle\DependencyInjection;

use Ruvents\DoctrineBundle\Metadata\CachedMetadataFactory;
use Ruvents\DoctrineBundle\Translations\TranslationsManager;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class RuventsDoctrineExtension extends ConfigurableExtension
{
    /**
     * {@inheritdoc}
     */
    public function loadInternal(array $config, ContainerBuilder $container)
    {
        (new PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config')))
            ->load('services.php');

        $container->findDefinition(CachedMetadataFactory::class)
            ->setArgument('$cache', new Reference($config['metadata_cache']));

        $container->findDefinition(TranslationsManager::class)
            ->setArgument('$defaultLocale', $config['default_locale']);
    }
}
