<?php

namespace Netgen\Bundle\InformationCollectionBundle\DependencyInjection;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\Configuration as SiteAccessConfiguration;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * {@inheritdoc}
 */
class Configuration extends SiteAccessConfiguration
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root(ConfigurationConstants::SETTINGS_ROOT);

        $this->generateScopeBaseNode($rootNode)
                ->arrayNode(ConfigurationConstants::ACTIONS)
                ->isRequired()
                ->normalizeKeys(false)
                    ->children()
                        ->arrayNode('default')
                            ->isRequired()
                            ->prototype('scalar')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                        ->end()

                        ->arrayNode(ConfigurationConstants::CONTENT_TYPES)
                            ->prototype('array')
                                ->prototype('scalar')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                ->end()
                            ->end()
                        ->end()

                    ->end()
                ->end()

                ->arrayNode(ConfigurationConstants::ACTION_CONFIG)
                ->isRequired()
                ->normalizeKeys(false)
                    ->children()
                        ->arrayNode('email')
                            ->children()
                                ->arrayNode('templates')
                                    ->children()
                                        ->scalarNode('default')
                                            ->isRequired()
                                            ->cannotBeEmpty()
                                        ->end()
                                        ->arrayNode(ConfigurationConstants::CONTENT_TYPES)
                                            ->prototype('scalar')
                                                ->isRequired()
                                                ->cannotBeEmpty()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                                ->arrayNode(ConfigurationConstants::DEFAULT_VARIABLES)
                                    ->children()
                                        ->scalarNode('sender')
                                            ->isRequired()
                                            ->cannotBeEmpty()
                                        ->end()
                                        ->scalarNode('recipient')
                                            ->isRequired()
                                            ->cannotBeEmpty()
                                        ->end()
                                        ->scalarNode('subject')
                                            ->cannotBeEmpty()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
