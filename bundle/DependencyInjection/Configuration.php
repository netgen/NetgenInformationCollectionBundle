<?php

namespace Netgen\Bundle\InformationCollectionBundle\DependencyInjection;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\Configuration as SiteAccessConfiguration;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

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

        $nodeBuilder = $this->generateScopeBaseNode($rootNode);
        $this->addCaptchaSection($nodeBuilder);
        $this->addActionsSection($nodeBuilder);
        $this->addActionConfigSection($nodeBuilder);
        $this->addCsvExportSection($nodeBuilder);

        $nodeBuilder->end();

        return $treeBuilder;
    }

    private function addCaptchaSection(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
            ->arrayNode(ConfigurationConstants::CAPTCHA)
                ->addDefaultsIfNotSet()
                ->children()
                    ->booleanNode('enabled')
                        ->defaultFalse()
                    ->end()
                    ->arrayNode('override_by_type')
                        ->prototype('array')
                            ->children()
                                ->booleanNode('enabled')->defaultFalse()->end()
                                ->scalarNode('secret')->cannotBeEmpty()->end()
                                ->scalarNode('site_key')->cannotBeEmpty()->end()
                                ->arrayNode('options')
                                    ->children()
                                        ->scalarNode('hostname')->cannotBeEmpty()->end()
                                        ->scalarNode('apk_package_name')->cannotBeEmpty()->end()
                                        ->scalarNode('action')->cannotBeEmpty()->end()
                                        ->scalarNode('score_threshold')->cannotBeEmpty()->end()
                                        ->scalarNode('challenge_timeout')->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->scalarNode('secret')
                        ->isRequired()
                    ->end()
                    ->scalarNode('site_key')
                        ->isRequired()
                    ->end()
                    ->arrayNode('options')
                        ->children()
                            ->scalarNode('hostname')
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('apk_package_name')
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('action')
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('score_threshold')
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('challenge_timeout')
                                ->cannotBeEmpty()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    private function addActionsSection(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
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
            ->end();
    }

    private function addActionConfigSection(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
            ->arrayNode(ConfigurationConstants::ACTION_CONFIG)
                ->isRequired()
                ->normalizeKeys(false)
                    ->children()
                        ->arrayNode(ConfigurationConstants::ACTION_AUTO_RESPONDER)
                            ->addDefaultsIfNotSet()
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
            ->end();
    }

    private function addCsvExportSection(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
            ->arrayNode(ConfigurationConstants::CSV_EXPORT)
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('delimiter')->defaultValue(',')->end()
                    // by default use windows line endings for compatibility with some csv libraries
                    ->scalarNode('newline')->defaultValue("\r\n")->end()
                    ->scalarNode('enclosure')->defaultValue("\"")->end()
                ->end()
            ->end();
    }
}
