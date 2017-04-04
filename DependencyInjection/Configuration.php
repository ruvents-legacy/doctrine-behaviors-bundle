<?php

namespace Ruvents\DoctrineBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ruvents_reform');

        $rootNode
            ->children()
                ->arrayNode('timestamp')
                    ->canBeEnabled()
                ->end()
                ->arrayNode('author')
                    ->canBeEnabled()
                    ->children()
                        ->scalarNode('provider_id')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('translatable')
                    ->canBeEnabled()
                    ->children()
                        ->arrayNode('fallbacks')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('use_date')
                    ->canBeEnabled()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
