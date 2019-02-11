<?php

namespace Netgen\Bundle\InformationCollectionBundle;

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
        $eZExtension->addPolicyProvider(new InformationCollectionPolicyProvider());
    }
}
