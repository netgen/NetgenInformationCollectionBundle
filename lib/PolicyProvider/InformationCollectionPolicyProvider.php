<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\PolicyProvider;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigBuilderInterface;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Security\PolicyProvider\PolicyProviderInterface;

class InformationCollectionPolicyProvider implements PolicyProviderInterface
{
    /**
     * @param ConfigBuilderInterface $configBuilder
     *
     * @return $this
     */
    public function addPolicies(ConfigBuilderInterface $configBuilder)
    {
        $configBuilder->addConfig([
            'infocollector' => [
                'read' => [],
                'delete' => [],
                'anonymize' => [],
                'export' => [],
            ],
        ]);

        return $this;
    }
}
