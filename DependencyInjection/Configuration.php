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
                ->arrayNode('timestampable')
                    ->canBeEnabled()
                ->end()
                ->arrayNode('translatable')
                    ->canBeEnabled()
                    ->children()
                        ->arrayNode('fallbacks')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
