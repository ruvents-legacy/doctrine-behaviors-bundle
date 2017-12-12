<?php

namespace Ruvents\DoctrineBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $builder = new TreeBuilder();

        // @formatter:off
        $builder
            ->root('ruvents_doctrine')
                ->children()
                    ->scalarNode('metadata_cache')
                        ->cannotBeEmpty()
                        ->defaultValue('cache.app');
        // @formatter:on

        return $builder;
    }
}
