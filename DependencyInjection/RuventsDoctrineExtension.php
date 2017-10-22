<?php

namespace Ruvents\DoctrineBundle\DependencyInjection;

use Ruvents\DoctrineBundle\Annotations\EventListener;
use Ruvents\DoctrineBundle\Annotations\Handler\AuthorHandler;
use Ruvents\DoctrineBundle\Annotations\Handler\AuthorStrategy\AuthorStrategyInterface;
use Ruvents\DoctrineBundle\Annotations\Handler\AuthorStrategy\TokenUserAuthorStrategy;
use Ruvents\DoctrineBundle\Annotations\Handler\HandlerInterface;
use Ruvents\DoctrineBundle\Annotations\Handler\PersistTimestampHandler;
use Ruvents\DoctrineBundle\Annotations\Handler\TimestampStrategy\ImmutableTimestampStrategy;
use Ruvents\DoctrineBundle\Annotations\Handler\TimestampStrategy\TimestampStrategyInterface;
use Ruvents\DoctrineBundle\Annotations\Handler\UpdateTimestampHandler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class RuventsDoctrineExtension extends ConfigurableExtension
{
    /**
     * {@inheritdoc}
     */
    public function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $container->autowire(EventListener::class)
            ->setPublic(false)
            ->addTag('doctrine.event_subscriber');

        $container->registerForAutoconfiguration(HandlerInterface::class)
            ->addTag('ruwork_doctrine.annotations_handler');

        $container->autowire(TokenUserAuthorStrategy::class)
            ->setPublic(false);

        $container->setAlias(AuthorStrategyInterface::class, TokenUserAuthorStrategy::class);

        $container->register(ImmutableTimestampStrategy::class)
            ->setPublic(false);

        $container->setAlias(TimestampStrategyInterface::class, ImmutableTimestampStrategy::class);

        $container->autowire(AuthorHandler::class)
            ->setPublic(false)
            ->addTag('ruwork_doctrine.annotations_handler');

        $container->autowire(PersistTimestampHandler::class)
            ->setPublic(false)
            ->addTag('ruwork_doctrine.annotations_handler');

        $container->autowire(UpdateTimestampHandler::class)
            ->setPublic(false)
            ->addTag('ruwork_doctrine.annotations_handler');
    }
}
