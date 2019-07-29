<?php

namespace Netgen\Bundle\InformationCollectionBundle;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\EzPublishCoreExtension;
use Netgen\InformationCollection\Container\Compiler\ActionsPass;
use Netgen\InformationCollection\Container\Compiler\CustomFieldHandlersPass;
use Netgen\InformationCollection\Container\Compiler\FieldAnonymizerVisitorPass;
use Netgen\InformationCollection\PolicyProvider\InformationCollectionPolicyProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class NetgenInformationCollectionBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ActionsPass());
        $container->addCompilerPass(new CustomFieldHandlersPass());
        $container->addCompilerPass(new FieldAnonymizerVisitorPass());

        $eZExtension = $container->getExtension('ezpublish');
        if ($eZExtension instanceof EzPublishCoreExtension) {
            $eZExtension->addPolicyProvider(new InformationCollectionPolicyProvider());
        }
    }
}
