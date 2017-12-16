<?php

declare(strict_types=1);

namespace Ruwork\DoctrineBehaviorsBundle\DependencyInjection;

use Doctrine\DBAL\Types\Type;
use Ruwork\DoctrineBehaviorsBundle\Strategy\AuthorStrategy\SecurityTokenAuthorStrategy;
use Ruwork\DoctrineBehaviorsBundle\Strategy\TimestampStrategy\FieldTypeTimestampStrategy;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    const CONNECTION_ANY = 'any';

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        /* @noinspection PhpIncompatibleReturnTypeInspection */
        // @formatter:off
        return (new TreeBuilder())
            ->root('ruwork_doctrine_behaviors')
                ->beforeNormalization()
                    ->ifTrue(function ($value): bool {
                        return is_array($value) && !array_key_exists('by_connection', $value);
                    })
                    ->then(function (array $value): array {
                        foreach (['author', 'multilingual', 'persist_timestamp', 'update_timestamp'] as $key) {
                            if (array_key_exists($key, $value)) {
                                $value['by_connection'][self::CONNECTION_ANY][$key] = $value[$key];
                                unset($value[$key]);
                            }
                        }

                        return $value;
                    })
                ->end()
                ->children()
                    ->arrayNode('by_connection')
                        ->cannotBeEmpty()
                        ->addDefaultChildrenIfNoneSet([
                            'connection' => self::CONNECTION_ANY,
                        ])
                        ->useAttributeAsKey('connection')
                        ->arrayPrototype()
                            ->children()
                                ->append($this->author())
                                ->append($this->multilingual())
                                ->append($this->timestamp('persist_timestamp', false))
                                ->append($this->timestamp('update_timestamp', true))
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
        // @formatter:on
    }

    private function author(): ArrayNodeDefinition
    {
        // @formatter:off
        return (new TreeBuilder())
            ->root('author')
                ->canBeDisabled()
                ->children()
                    ->scalarNode('strategy')
                        ->cannotBeEmpty()
                        ->defaultValue(SecurityTokenAuthorStrategy::class)
                    ->end()
                    ->append($this->defaultMapping()
                        ->children()
                            ->append($this->manyToOne()->canBeEnabled())
                            ->append($this->field(Type::STRING, false)->canBeEnabled())
                        ->end()
                    )
                ->end();
        // @formatter:on
    }

    private function multilingual(): ArrayNodeDefinition
    {
        // @formatter:off
        return (new TreeBuilder())
            ->root('multilingual')
                ->canBeDisabled()
                ->children()
                    ->scalarNode('default_locale')
                        ->cannotBeEmpty()
                        ->defaultValue('%kernel.default_locale%')
                    ->end()
                    ->append($this->defaultMapping()
                        ->children()
                            //->append($this->manyToOne()->canBeEnabled())
                            ->append($this->embedded()/*->canBeEnabled()*/)
                        ->end()
                    )
                ->end();
        // @formatter:on
    }

    private function timestamp(string $name, bool $nullableDefault): ArrayNodeDefinition
    {
        // @formatter:off
        return (new TreeBuilder())
            ->root($name)
                ->canBeDisabled()
                ->children()
                    ->scalarNode('strategy')
                        ->cannotBeEmpty()
                        ->defaultValue(FieldTypeTimestampStrategy::class)
                    ->end()
                    ->append($this->defaultMapping()
                        ->children()
                            ->append($this->field(Type::DATETIMETZ_IMMUTABLE, $nullableDefault))
                        ->end()
                    )
                ->end();
        // @formatter:on
    }

    private function defaultMapping()
    {
        // @formatter:off
        return (new TreeBuilder())
            ->root('default_mapping')
                ->canBeEnabled()
                ->validate()
                    ->always(function (array $value): array {
                        $variants = [];
                        $enabledVariants = [];

                        foreach ($value as $variant => $mapping) {
                            if (is_array($mapping)) {
                                if ($mapping['enabled'] ?? true) {
                                    $enabledVariants[] = $variant;
                                }

                                $variants[] = $variant;
                            }
                        }

                        if (1 !== count($enabledVariants)) {
                            throw new \InvalidArgumentException(sprintf('Exactly one of the mapping variants among "%s" must be enabled.', implode('", "', $variants)));
                        }

                        return $value + ['enabled_variant' => $enabledVariants[0]];
                    })
                ->end();
        // @formatter:on
    }

    private function field(string $type, bool $nullableDefault): ArrayNodeDefinition
    {
        // @formatter:off
        return (new TreeBuilder())
            ->root('field')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('type')
                        ->cannotBeEmpty()
                        ->defaultValue($type)
                    ->end()
                    ->booleanNode('nullable')
                        ->defaultValue($nullableDefault)
                    ->end()
                    ->integerNode('length')
                        ->min(0)
                    ->end()
                ->end();
        // @formatter:on
    }

    private function manyToOne(): ArrayNodeDefinition
    {
        // @formatter:off
        return (new TreeBuilder())
            ->root('many_to_one')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('target_entity')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->booleanNode('nullable')
                        ->defaultFalse()
                    ->end()
                    ->enumNode('fetch')
                        ->values(['LAZY', 'EAGER', 'EXTRA_LAZY'])
                        ->cannotBeEmpty()
                        ->defaultValue('LAZY')
                    ->end()
                ->end();
        // @formatter:on
    }

    private function embedded(): ArrayNodeDefinition
    {
        // @formatter:off
        return (new TreeBuilder())
            ->root('embedded')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('class')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                ->end();
        // @formatter:on
    }
}
