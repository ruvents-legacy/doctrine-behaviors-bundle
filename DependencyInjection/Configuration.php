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
                    ->arrayNode('default_mapping')
                        ->canBeEnabled()
                        ->beforeNormalization()
                            ->ifTrue(function ($value): bool {
                                return is_array($value) && !array_key_exists('association', $value) && !array_key_exists('field', $value);
                            })
                            ->then(function (array $value): array {
                                if (array_key_exists('nullable', $value)) {
                                    $section = array_key_exists('target_entity', $value) ? 'association' : 'field';
                                    $value[$section]['nullable'] = $value['nullable'];
                                    unset($value['nullable']);
                                }

                                foreach (['target_entity', 'fetch'] as $key) {
                                    if (array_key_exists($key, $value)) {
                                        $value['association'][$key] = $value[$key];
                                        unset($value[$key]);
                                    }
                                }

                                foreach (['type', 'length'] as $key) {
                                    if (array_key_exists($key, $value)) {
                                        $value['field'][$key] = $value[$key];
                                        unset($value[$key]);
                                    }
                                }

                                return $value;
                            })
                        ->end()
                        ->children()
                            ->append($this->association())
                            ->append($this->field(Type::STRING, false))
                        ->end()
                        ->validate()
                            ->ifTrue(function (array $value): bool {
                                return !($value['association']['enabled'] xor $value['field']['enabled']);
                            })
                            ->thenInvalid('Association xor field options must be provided.')
                        ->end()
                    ->end()
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
                    ->arrayNode('default_mapping')
                        ->canBeEnabled()
                        ->beforeNormalization()
                            ->ifTrue(function ($value): bool {
                                return is_array($value) && !array_key_exists('association', $value) && !array_key_exists('embedded', $value);
                            })
                            ->then(function (array $value): array {
                                /*foreach (['target_entity', 'nullable', 'fetch'] as $key) {
                                    if (array_key_exists($key, $value)) {
                                        $value['association'][$key] = $value[$key];
                                        unset($value[$key]);
                                    }
                                }*/

                                if (array_key_exists('class', $value)) {
                                    $value['embedded']['class'] = $value['class'];
                                    unset($value['class']);
                                }

                                return $value;
                            })
                        ->end()
                        ->children()
                            //->append($this->association())
                            ->append($this->embedded())
                        ->end()
                        ->validate()
                            ->ifTrue(function (array $value): bool {
                                return !($value['association']['enabled'] xor $value['embedded']['enabled']);
                            })
                            ->thenInvalid('Association xor embedded options must be provided.')
                        ->end()
                    ->end()
                ->end();
        // @formatter:on
    }

    private function timestamp(string $name, bool $nullableDefaultValue): ArrayNodeDefinition
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
                    ->arrayNode('default_mapping')
                        ->append($this->field(Type::DATETIMETZ_IMMUTABLE, $nullableDefaultValue))
                    ->end()
                ->end();
        // @formatter:on
    }

    private function association(): ArrayNodeDefinition
    {
        // @formatter:off
        return (new TreeBuilder())
            ->root('association')
                ->canBeEnabled()
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
                ->canBeEnabled()
                ->children()
                    ->scalarNode('class')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                ->end();
        // @formatter:on
    }

    private function field(string $type, bool $nullableDefaultValue): ArrayNodeDefinition
    {
        // @formatter:off
        return (new TreeBuilder())
            ->root('field')
                ->canBeEnabled()
                ->children()
                    ->scalarNode('type')
                        ->cannotBeEmpty()
                        ->defaultValue($type)
                    ->end()
                    ->booleanNode('nullable')
                        ->defaultValue($nullableDefaultValue)
                    ->end()
                    ->integerNode('length')
                        ->min(0)
                    ->end()
                ->end();
        // @formatter:on
    }
}
