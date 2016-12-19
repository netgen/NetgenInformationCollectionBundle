<?php

namespace Netgen\Bundle\InformationCollectionBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ConfigurationProcessor;

/**
 * This is the class that loads and manages your bundle configuration
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

        $processor = new ConfigurationProcessor($container, 'netgen_information_collection');
        $configArrays = array('actions');
        $scopes = array_merge(array('default'), $container->getParameter('ezpublish.siteaccess.list'));
        foreach ($configArrays as $configArray) {
            $processor->mapConfigArray($configArray, $config);
            foreach ($scopes as $scope) {
                $scopeConfig = $container->getParameter('netgen_information_collection.' . $scope . '.' . $configArray);
                foreach ($scopeConfig as $key => $value) {
                    $container->setParameter(
                        'netgen_information_collection.' . $scope . '.' . $configArray . '.' . $key,
                        $value
                    );
                }
            }
        }
    }
}
