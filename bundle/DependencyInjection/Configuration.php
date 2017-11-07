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
                        ->arrayNode(ConfigurationConstants::SETTINGS_DEFAULT)
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
                        ->arrayNode(ConfigurationConstants::ACTION_AUTO_RESPONDER)
                            ->children()
                                ->arrayNode(ConfigurationConstants::TEMPLATES)
                                    ->children()
                                        ->scalarNode(ConfigurationConstants::SETTINGS_DEFAULT)
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
                                        ->scalarNode(ConfigurationConstants::EMAIL_SENDER)
                                            ->isRequired()
                                            ->cannotBeEmpty()
                                        ->end()
                                        ->scalarNode(ConfigurationConstants::EMAIL_SUBJECT)
                                            ->isRequired()
                                            ->cannotBeEmpty()
                                        ->end()
                                        ->scalarNode(ConfigurationConstants::EMAIL_FIELD_IDENTIFIER)
                                            ->defaultValue('email')
                                            ->cannotBeEmpty()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode(ConfigurationConstants::ACTION_EMAIL)
                            ->children()
                                ->arrayNode(ConfigurationConstants::TEMPLATES)
                                    ->children()
                                        ->scalarNode(ConfigurationConstants::SETTINGS_DEFAULT)
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
                                ->arrayNode(ConfigurationConstants::ATTACHMENTS)
                                    ->children()
                                        ->scalarNode(ConfigurationConstants::SETTINGS_DEFAULT)
                                            ->isRequired()
                                            ->defaultValue(true)
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
                                        ->scalarNode(ConfigurationConstants::EMAIL_SENDER)
                                            ->isRequired()
                                            ->cannotBeEmpty()
                                        ->end()
                                        ->scalarNode(ConfigurationConstants::EMAIL_RECIPIENT)
                                            ->isRequired()
                                            ->cannotBeEmpty()
                                        ->end()
                                        ->scalarNode(ConfigurationConstants::EMAIL_SUBJECT)
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
