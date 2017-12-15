<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Doctrine\ORM\Events as ORMEvents;
use Ruwork\DoctrineBehaviorsBundle\EventListener\AuthorListener;
use Ruwork\DoctrineBehaviorsBundle\EventListener\PersistTimestampListener;
use Ruwork\DoctrineBehaviorsBundle\EventListener\MultilingualListener;
use Ruwork\DoctrineBehaviorsBundle\EventListener\UpdateTimestampListener;
use Ruwork\DoctrineBehaviorsBundle\Metadata\LazyLoadingMetadataFactory;
use Ruwork\DoctrineBehaviorsBundle\Metadata\MetadataFactory;
use Ruwork\DoctrineBehaviorsBundle\Metadata\MetadataFactoryInterface;
use Ruwork\DoctrineBehaviorsBundle\Strategy\AuthorStrategy\AuthorStrategyInterface;
use Ruwork\DoctrineBehaviorsBundle\Strategy\AuthorStrategy\SecurityTokenAuthorStrategy;
use Ruwork\DoctrineBehaviorsBundle\Strategy\TimestampStrategy\FieldTypeTimestampStrategy;
use Ruwork\DoctrineBehaviorsBundle\Strategy\TimestampStrategy\TimestampStrategyInterface;
use Symfony\Component\HttpKernel\KernelEvents;

return function (ContainerConfigurator $container): void {
    $container->services()
        ->set('cache.ruwork.doctrine_behaviors')
        ->parent('cache.system')
        ->private()
        ->tag('cache.pool');

    $services = $container->services()
        ->defaults()
        ->private();

    $services->set(MetadataFactory::class)
        ->args([
            '$annotationReader' => ref('annotation_reader'),
        ]);

    $services->set(LazyLoadingMetadataFactory::class)
        ->args([
            '$factory' => ref(MetadataFactory::class),
            '$cache' => ref('cache.ruwork.doctrine_behaviors'),
        ]);

    $services->alias(MetadataFactoryInterface::class, LazyLoadingMetadataFactory::class);

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
        ->tag('doctrine.event_listener', ['event' => ORMEvents::prePersist, 'lazy' => true]);

    $services->set(PersistTimestampListener::class)
        ->args([
            '$factory' => ref(MetadataFactoryInterface::class),
            '$strategy' => ref(TimestampStrategyInterface::class),
        ])
        ->tag('doctrine.event_listener', ['event' => ORMEvents::prePersist, 'lazy' => true]);

    $services->set(MultilingualListener::class)
        ->args([
            '$factory' => ref(MetadataFactoryInterface::class),
            '$requestStack' => ref('request_stack'),
        ])
        ->tag('kernel.event_listener', ['event' => KernelEvents::REQUEST])
        ->tag('doctrine.event_listener', ['event' => ORMEvents::prePersist, 'lazy' => true])
        ->tag('doctrine.event_listener', ['event' => ORMEvents::postLoad, 'lazy' => true]);

    $services->set(UpdateTimestampListener::class)
        ->args([
            '$factory' => ref(MetadataFactoryInterface::class),
            '$strategy' => ref(TimestampStrategyInterface::class),
        ])
        ->tag('doctrine.event_listener', ['event' => ORMEvents::preUpdate, 'lazy' => true]);
};
