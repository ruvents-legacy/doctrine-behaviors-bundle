<?php

declare(strict_types=1);

namespace Ruwork\DoctrineBehaviorsBundle\DependencyInjection;

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
            ->root('ruwork_doctrine_behaviors')
                ->children()
                    ->scalarNode('default_locale')
                        ->cannotBeEmpty()
                        ->defaultValue('%kernel.default_locale%');
        // @formatter:on

        return $builder;
    }
}
