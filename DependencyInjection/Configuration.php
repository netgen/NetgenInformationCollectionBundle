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
            ->arrayNode('actions_configuration')
                ->useAttributeAsKey('content_type')
                ->normalizeKeys(false)
                ->prototype('array')
                    ->requiresAtLeastOneElement()
                    ->normalizeKeys(false)
                    ->prototype('array')
                        ->children()
                            ->scalarNode('action')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();


        return $treeBuilder;
    }
}
