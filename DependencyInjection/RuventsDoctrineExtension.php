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
use Ruvents\DoctrineBundle\Annotations\Handler\TranslatableHandler;
use Ruvents\DoctrineBundle\Annotations\Handler\UpdateTimestampHandler;
use Ruvents\DoctrineBundle\Translations\TranslationsManager;
use Ruvents\DoctrineBundle\Validator\TranslationsValidator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Validation;

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

        $container->autowire(AuthorHandler::class)
            ->setPublic(false)
            ->addTag('ruwork_doctrine.annotations_handler');

        if (class_exists(Security::class)) {
            $container->autowire(TokenUserAuthorStrategy::class)
                ->setPublic(false);

            $container->setAlias(AuthorStrategyInterface::class, TokenUserAuthorStrategy::class);
        }

        $container->register(ImmutableTimestampStrategy::class)
            ->setPublic(false);

        $container->setAlias(TimestampStrategyInterface::class, ImmutableTimestampStrategy::class);

        $container->autowire(PersistTimestampHandler::class)
            ->setPublic(false)
            ->addTag('ruwork_doctrine.annotations_handler');

        $container->autowire(UpdateTimestampHandler::class)
            ->setPublic(false)
            ->addTag('ruwork_doctrine.annotations_handler');

        $container->autowire(TranslatableHandler::class)
            ->setPublic(false)
            ->addTag('ruwork_doctrine.annotations_handler');

        $container->autowire(TranslationsManager::class)
            ->setPublic(false)
            ->setArgument('$defaultLocale', '%kernel.default_locale%')
            ->addTag('kernel.event_listener', ['event' => KernelEvents::REQUEST]);

        if (class_exists(Validation::class)) {
            $container->autowire(TranslationsValidator::class)
                ->setPublic(false)
                ->addTag('validator.constraint_validator');
        }
    }
}
