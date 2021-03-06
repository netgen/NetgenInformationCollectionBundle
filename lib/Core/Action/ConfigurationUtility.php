<?php

namespace Netgen\InformationCollection\Core\Action;

use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Netgen\InformationCollection\API\Action\ActionInterface;
use OutOfBoundsException;
use function sprintf;
use function in_array;

final class ConfigurationUtility
{
    /**
     * @var array
     */
    private $actionsdConfiguration;

    /**
     * @var array
     */
    private $singleActionConfiguration;

    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->actionsdConfiguration = $configResolver->getParameter('actions', 'netgen_information_collection');
        $this->singleActionConfiguration = $configResolver->getParameter('action_config', 'netgen_information_collection');
    }

    public function isActionAllowedToRun(ActionInterface $action, array $config): bool
    {
        $identifier = new Identifier($action);

        if (in_array($identifier->getPrimary(), $config, false)) {
            return true;
        }

        if (in_array($identifier->getSecondary(), $config, false)) {
            return true;
        }

        return false;
    }

    public function getActionConfiguration(ActionInterface $action): array
    {
        $identifier = new Identifier($action);

        if (in_array($identifier->getPrimary(), $this->singleActionConfiguration, false)) {
            return $this->singleActionConfiguration[$identifier->getPrimary()];
        }

        if (in_array($identifier->getSecondary(), $this->singleActionConfiguration, false)) {
            return $this->singleActionConfiguration[$identifier->getSecondary()];
        }

        throw new OutOfBoundsException(sprintf('There is no configuration available for %s', $identifier->getSecondary()));
    }

    /**
     * Returns configuration for given content type identifier if exists
     * or default one.
     *
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType
     *
     * @return array
     */
    public function getConfigPerContentType(ContentType $contentType): array
    {
        if (!empty($this->actionsdConfiguration['content_types'][$contentType->identifier])) {
            return $this->actionsdConfiguration['content_types'][$contentType->identifier];
        }

        if (!empty($this->actionsdConfiguration['default'])) {
            return $this->actionsdConfiguration['default'];
        }

        return [];
    }
}
