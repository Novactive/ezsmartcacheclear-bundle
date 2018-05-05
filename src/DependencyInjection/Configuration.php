<?php
/**
 * File part of the eZSmartCacheClearBundle package.
 *
 * @package   Novactive/eZSmartCacheCLearBundle
 *
 * @copyright 2018 Novactive
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Novactive\eZSmartCacheClearBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Bundle Configuration définition class.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('nova_ez_smart_cache_clear');

        $rootNode
            ->children()
                ->arrayNode('config')
                    ->useAttributeAsKey('siteaccess_name')
                    ->requiresAtLeastOneElement()
                    ->normalizeKeys(false)
                    ->arrayPrototype()
                        ->children()
                            ->arrayNode('publish')
                                ->arrayPrototype()
                                    ->children()
                                        ->scalarNode('content_type')->end()
                                        ->arrayNode('rules')
                                            ->children()
                                                ->arrayNode('parents')
                                                    ->children()
                                                        ->booleanNode('enabled')->end()
                                                        ->integerNode('nbLevels')->end()
                                                    ->end()
                                                ->end()
                                                ->arrayNode('children')
                                                    ->children()
                                                        ->booleanNode('enabled')->end()
                                                    ->end()
                                                ->end()
                                                ->arrayNode('siblings')
                                                    ->children()
                                                        ->booleanNode('enabled')->end()
                                                    ->end()
                                                ->end()
                                                ->arrayNode('subtree')
                                                    ->children()
                                                        ->booleanNode('enabled')->end()
                                                    ->end()
                                                ->end();

        return $treeBuilder;
    }
}
