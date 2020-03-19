<?php

namespace Netgen\Bundle\InformationCollectionBundle;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\EzPublishCoreExtension;
use Netgen\Bundle\InformationCollectionBundle\DependencyInjection\Compiler\ExportResponseFormattersPass;
use Netgen\Bundle\InformationCollectionBundle\PolicyProvider\InformationCollectionPolicyProvider;
use Netgen\Bundle\InformationCollectionBundle\DependencyInjection\Compiler\ActionsPass;
use Netgen\Bundle\InformationCollectionBundle\DependencyInjection\Compiler\CustomFieldHandlersPass;
use Netgen\Bundle\InformationCollectionBundle\DependencyInjection\Compiler\FieldAnonymizerVisitorPass;
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
        $container->addCompilerPass(new ExportResponseFormattersPass());

        $eZExtension = $container->getExtension('ezpublish');
        if ($eZExtension instanceof EzPublishCoreExtension) {
            $eZExtension->addPolicyProvider(new InformationCollectionPolicyProvider());
        }
    }
}
