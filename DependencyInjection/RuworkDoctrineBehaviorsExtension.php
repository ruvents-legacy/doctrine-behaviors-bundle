<?php

declare(strict_types=1);

namespace Ruwork\DoctrineBehaviorsBundle\DependencyInjection;

use Ruwork\DoctrineBehaviorsBundle\EventListener\TranslatableListener;
use Ruwork\DoctrineBehaviorsBundle\Metadata\LazyLoadingMetadataFactory;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class RuworkDoctrineBehaviorsExtension extends ConfigurableExtension
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
