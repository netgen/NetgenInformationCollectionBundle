<?php

namespace Netgen\Bundle\InformationCollectionBundle;

use Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension;
use Netgen\Bundle\InformationCollectionBundle\Ibexa\PolicyProvider\InformationCollectionPolicyProvider;
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

        $eZExtension = $container->getExtension('ibexa');
        if ($eZExtension instanceof IbexaCoreExtension) {
            $eZExtension->addPolicyProvider(new InformationCollectionPolicyProvider());
        }
    }
}
