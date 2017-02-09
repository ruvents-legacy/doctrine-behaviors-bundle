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
                ->arrayNode('translatable_field')
                    ->canBeEnabled()
                    ->children()
                        ->scalarNode('default_locale')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
