<?php

namespace Netgen\Bundle\InformationCollectionBundle\DependencyInjection;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ConfigurationProcessor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class NetgenInformationCollectionExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('parameters.yml');

        $processor = new ConfigurationProcessor($container, ConfigurationConstants::SETTINGS_ROOT);
        $configArrays = array(
            ConfigurationConstants::ACTIONS,
            ConfigurationConstants::ACTION_CONFIG,
        );

        $scopes = array_merge(array('default'), $container->getParameter('ezpublish.siteaccess.list'));

        foreach ($configArrays as $configArray) {
            $processor->mapConfigArray($configArray, $config);
            foreach ($scopes as $scope) {
                $scopeConfig = $container->getParameter(
                    ConfigurationConstants::SETTINGS_ROOT . '.' . $scope . '.' . $configArray
                );
                foreach ((array) $scopeConfig as $key => $value) {
                    $container->setParameter(
                        ConfigurationConstants::SETTINGS_ROOT . '.' . $scope . '.' . $configArray . '.' . $key,
                        $value
                    );
                }
            }
        }
    }
}
