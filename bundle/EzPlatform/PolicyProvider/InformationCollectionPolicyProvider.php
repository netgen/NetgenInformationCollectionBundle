<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\EzPlatform\PolicyProvider;

use Netgen\InformationCollection\API\Permissions;
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
            Permissions::NAME => [
                Permissions::POLICY_READ => [],
                Permissions::POLICY_DELETE => [],
                Permissions::POLICY_ANONYMIZE => [],
                Permissions::POLICY_EXPORT => [],
            ],
        ]);

        return [];
    }
}
