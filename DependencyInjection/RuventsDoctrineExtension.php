<?php

declare(strict_types=1);

namespace Ruvents\DoctrineBundle\DependencyInjection;

use Ruvents\DoctrineBundle\EventListener\TranslatableListener;
use Ruvents\DoctrineBundle\Metadata\LazyLoadingMetadataFactory;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class RuventsDoctrineExtension extends ConfigurableExtension
{
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

        $container->findDefinition(TranslatableListener::class)
            ->setArgument('$defaultLocale', $config['default_locale']);
    }
}
