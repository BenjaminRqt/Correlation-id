<?php

declare(strict_types=1);

namespace BenjaminRqt\CorrelationIdBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('correlation_id');

        /* @phpstan-ignore-next-line */
        $treeBuilder->getRootNode()
            ->children()
            ->scalarNode('header_name')
            ->defaultValue('X-Correlation-ID')
            ->end()
            ->end();

        return $treeBuilder;
    }
}
