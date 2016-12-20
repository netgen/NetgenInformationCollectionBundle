<?php

namespace Netgen\Bundle\InformationCollectionBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\Configuration as SiteAccessConfiguration;

/**
 * This is the class that validates and merges configuration from your app/config files
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
        $rootNode = $treeBuilder->root("netgen_information_collection");

        $this->generateScopeBaseNode($rootNode)
                ->arrayNode(ConfigurationConstants::ACTIONS)
                ->isRequired()
                ->normalizeKeys(false)
                    ->children()
                        ->arrayNode('default')
                            ->isRequired()
                            ->prototype('scalar')
                            ->end()
                        ->end()

                        ->arrayNode('content_type')
                            ->children()
                            ->end()
                        ->end()

                    ->end()
                ->end()

                ->arrayNode(ConfigurationConstants::TEMPLATES)
                ->isRequired()
                ->normalizeKeys(false)
                    ->children()
                        ->arrayNode('default')
                            ->isRequired()
                            ->prototype('scalar')
                            ->end()
                        ->end()
                        ->arrayNode('content_type')
                            ->prototype('array')
                                ->scalarNode('template')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode(ConfigurationConstants::FALLBACK_VALUES)
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
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
