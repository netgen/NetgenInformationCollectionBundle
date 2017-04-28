<?php

namespace Netgen\Bundle\InformationCollectionBundle\DependencyInjection;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\Configuration as SiteAccessConfiguration;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
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

                        ->arrayNode('content_types')
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
                                        ->arrayNode('content_types')
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
