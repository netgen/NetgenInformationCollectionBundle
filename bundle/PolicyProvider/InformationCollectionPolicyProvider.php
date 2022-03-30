<?php

namespace Netgen\Bundle\InformationCollectionBundle\PolicyProvider;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigBuilderInterface;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Security\PolicyProvider\PolicyProviderInterface;

class InformationCollectionPolicyProvider implements PolicyProviderInterface
{
    /**
     * @param \eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigBuilderInterface $configBuilder
     *
     * @return array
     */
    public function addPolicies(ConfigBuilderInterface $configBuilder)
    {
        $configBuilder->addConfig([
            'infocollector' => [
                'read' => [],
                'edit' => [],
                'delete' => [],
                'anonymize' => [],
                'export' => [],
            ],
        ]);

        return [];
    }
}
