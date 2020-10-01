<?php

namespace Netgen\Bundle\InformationCollectionBundle;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\EzPublishCoreExtension;
use Netgen\Bundle\InformationCollectionBundle\EzPlatform\PolicyProvider\InformationCollectionPolicyProvider;
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

        $eZExtension = $container->getExtension('ezpublish');
        if ($eZExtension instanceof EzPublishCoreExtension) {
            $eZExtension->addPolicyProvider(new InformationCollectionPolicyProvider());
        }
    }
}
