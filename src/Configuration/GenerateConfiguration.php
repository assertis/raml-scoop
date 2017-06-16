<?php
declare(strict_types=1);

namespace Assertis\RamlScoop\Configuration;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class GenerateConfiguration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('config');

        $rootNode->children()
            ->scalarNode('name')->isRequired()->end()
            ->scalarNode('output')
                ->defaultValue('tmp/Documentation')
            ->end()
            ->scalarNode('theme')
                ->defaultValue('resources/DefaultTheme')
            ->end()
            ->arrayNode('formats')
                ->prototype('scalar')->end()
                ->defaultValue(['html', 'pdf', 'zip'])
            ->end()
            ->arrayNode('sources')
                ->prototype('array')
                    ->children()
                        ->scalarNode('name')->isRequired()->end()
                        ->scalarNode('prefix')
                            ->defaultValue('')
                        ->end()
                        ->scalarNode('path')->end()
                        ->arrayNode('git')
                            ->children()
                                ->scalarNode('uri')->isRequired()->end()
                                ->scalarNode('branch')->isRequired()->end()
                                ->scalarNode('path')->isRequired()->end()
                            ->end()
                        ->end()
                        ->arrayNode('exclude')
                            ->prototype('scalar')->end()
                            ->defaultValue([])
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
