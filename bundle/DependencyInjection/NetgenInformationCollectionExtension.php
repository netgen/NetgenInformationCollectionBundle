<?php

namespace Netgen\Bundle\InformationCollectionBundle\DependencyInjection;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ConfigurationProcessor;
use Netgen\InformationCollection\API\ConfigurationConstants;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Yaml\Yaml;
use function array_merge;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class NetgenInformationCollectionExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $bundleResourceLoader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $bundleResourceLoader->load('services.yml');
//        $loader->load('parameters.yml');
//        $loader->load('default_settings.yml');


        $libResourceLoader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../../lib/Resources/config'));
        $libResourceLoader->load('services.yml');
        $libResourceLoader->load('parameters.yml');
        $libResourceLoader->load('default_settings.yml');

        $processor = new ConfigurationProcessor($container, ConfigurationConstants::SETTINGS_ROOT);
        $configArrays = array(
            ConfigurationConstants::ACTIONS,
            ConfigurationConstants::ACTION_CONFIG,
        );

        $scopes = array_merge(array('default'), $container->getParameter('ezpublish.siteaccess.list'));

        foreach ($configArrays as $configArray) {
            $processor->mapConfigArray($configArray, $config);
            foreach ($scopes as $scope) {
                $paramName = ConfigurationConstants::SETTINGS_ROOT . '.' . $scope . '.' . $configArray;
                if (!$container->hasParameter($paramName)) {
                    continue;
                }

                $scopeConfig = $container->getParameter($paramName);
                foreach ((array) $scopeConfig as $key => $value) {
                    $container->setParameter($paramName . '.' . $key, $value);
                }
            }
        }
    }

    public function prepend(ContainerBuilder $container)
    {
        $this->addTwigConfig($container);
        $this->addDoctrineConfig($container);
    }

    protected function addDoctrineConfig(ContainerBuilder $container)
    {
        $configDir = __DIR__ . '/../../lib/Doctrine/mappings';

        $config = [
            'orm' => [
                'auto_mapping' => true,
                'mappings' => [
                    'NetgenInformationCollectionBundle' => [
                        'is_bundle' => false,
                        'dir' => $configDir,
                        'type' => 'xml',
                        'prefix' => 'Netgen\InformationCollection\Doctrine\Entity'
                    ]
                ]
            ]
        ];

        $container->prependExtensionConfig('doctrine', $config);
    }

    protected function addTwigConfig(ContainerBuilder $container)
    {
        $configs = array(
            'twig.yml' => 'twig',
        );

        $activatedBundles = array_keys($container->getParameter('kernel.bundles'));

        foreach ($configs as $fileName => $extensionName) {
            $configFile = __DIR__ . '/../../lib/Resources/config/' . $fileName;
            $config = Yaml::parse((string)file_get_contents($configFile));
            $container->prependExtensionConfig($extensionName, $config);
            $container->addResource(new FileResource($configFile));
        }
    }
}
