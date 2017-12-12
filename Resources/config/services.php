<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Ruvents\DoctrineBundle\EventListener\AuthorListener;
use Ruvents\DoctrineBundle\EventListener\PersistTimestampListener;
use Ruvents\DoctrineBundle\EventListener\TranslatableListener;
use Ruvents\DoctrineBundle\EventListener\UpdateTimestampListener;
use Ruvents\DoctrineBundle\Metadata\CachedMetadataFactory;
use Ruvents\DoctrineBundle\Metadata\MetadataFactory;
use Ruvents\DoctrineBundle\Metadata\MetadataFactoryInterface;
use Ruvents\DoctrineBundle\Strategy\AuthorStrategy\AuthorStrategyInterface;
use Ruvents\DoctrineBundle\Strategy\AuthorStrategy\SecurityTokenAuthorStrategy;
use Ruvents\DoctrineBundle\Strategy\TimestampStrategy\FieldTypeTimestampStrategy;
use Ruvents\DoctrineBundle\Strategy\TimestampStrategy\TimestampStrategyInterface;
use Ruvents\DoctrineBundle\Translations\TranslationsManager;
use Symfony\Component\HttpKernel\KernelEvents;

return function (ContainerConfigurator $container) {
    $services = $container->services()
        ->defaults()
        ->private();

    $services->set(MetadataFactory::class)
        ->args([
            '$annotationReader' => ref('annotation_reader'),
            '$doctrine' => ref('doctrine'),
        ]);

    $services->set(CachedMetadataFactory::class)
        ->args([
            '$factory' => ref(MetadataFactory::class),
            '$debug' => '%kernel.debug%',
        ]);

    $services->alias(MetadataFactoryInterface::class, CachedMetadataFactory::class);

    $services->set(SecurityTokenAuthorStrategy::class)
        ->args([
            '$tokenStorage' => ref('security.token_storage'),
        ]);

    $services->alias(AuthorStrategyInterface::class, SecurityTokenAuthorStrategy::class);

    $services->set(FieldTypeTimestampStrategy::class);

    $services->alias(TimestampStrategyInterface::class, FieldTypeTimestampStrategy::class);

    $services->set(AuthorListener::class)
        ->args([
            '$factory' => ref(MetadataFactoryInterface::class),
            '$strategy' => ref(AuthorStrategyInterface::class),
        ])
        ->tag('doctrine.event_subscriber');

    $services->set(PersistTimestampListener::class)
        ->args([
            '$factory' => ref(MetadataFactoryInterface::class),
            '$strategy' => ref(TimestampStrategyInterface::class),
        ])
        ->tag('doctrine.event_subscriber');

    $services->set(UpdateTimestampListener::class)
        ->args([
            '$factory' => ref(MetadataFactoryInterface::class),
            '$strategy' => ref(TimestampStrategyInterface::class),
        ])
        ->tag('doctrine.event_subscriber');

    $services->set(TranslationsManager::class)
        ->args([
            '$requestStack' => ref('request_stack'),
        ])
        ->tag('kernel.event_listener', ['event' => KernelEvents::REQUEST]);

    $services->set(TranslatableListener::class)
        ->args([
            '$factory' => ref(MetadataFactoryInterface::class),
            '$manager' => ref(TranslationsManager::class),
        ])
        ->tag('doctrine.event_subscriber');
};
