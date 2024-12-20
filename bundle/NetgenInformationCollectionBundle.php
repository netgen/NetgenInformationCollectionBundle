<?php

namespace Netgen\Bundle\InformationCollectionBundle;

use Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension;
use Netgen\Bundle\InformationCollectionBundle\DependencyInjection\Compiler\FieldTypeHandlerRegistryPass;
use Netgen\Bundle\InformationCollectionBundle\Ibexa\PolicyProvider\InformationCollectionPolicyProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class NetgenInformationCollectionBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new FieldTypeHandlerRegistryPass());
        parent::build($container);

        $ibexaExtension = $container->getExtension('ibexa');
        if ($ibexaExtension instanceof IbexaCoreExtension) {
            $ibexaExtension->addPolicyProvider(new InformationCollectionPolicyProvider());
        }
    }
}
