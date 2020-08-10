<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Action;

use function in_array;
use Netgen\InformationCollection\API\Action\ActionInterface;
use Netgen\InformationCollection\API\Action\CrucialActionInterface;
use Netgen\InformationCollection\API\Exception\ActionFailedException;
use Netgen\InformationCollection\API\Priority;
use Netgen\InformationCollection\API\Value\Event\InformationCollected;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Psr\Log\LoggerInterface;
use function usort;

class ActionRegistry implements ActionInterface
{
    /**
     * @var \eZ\Publish\Core\MVC\ConfigResolverInterface
     */
    protected $configResolver;

    /**
     * @var array
     */
    protected $actions;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * ActionAggregate constructor.
     *
     * @param array $config
     * @param LoggerInterface $logger
     */
    public function __construct(iterable $actions, ConfigResolverInterface $configResolver, LoggerInterface $logger)
    {
        $this->actions = $actions;
        $this->configResolver = $configResolver;
        $this->config = $configResolver->getParameter('actions', 'netgen_information_collection');
        $this->logger = $logger;
    }

    public function act(InformationCollected $event): void
    {
        $config = $this->prepareConfig($event->getContentType()->identifier);

        foreach ($this->actions as $action) {
            if ($this->canActionAct($action, $config)) {
                try {
                    $action->act($event);
                } catch (ActionFailedException $e) {
                    $this->logger
                        ->error($e->getMessage());

                    if ($this->debug) {
                        throw $e;
                    }

                    if ($action instanceof CrucialActionInterface) {
                        break;
                    }
                }
            }
        }
    }

    /**
     * Sets debug variable based on kernel.debug param.
     *
     * @param bool $debug
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    /**
     * Check if given action can act.
     *
     * @param ActionInterface $action
     * @param array $config
     *
     * @return bool
     */
    protected function canActionAct(ActionInterface $action, array $config): bool
    {
        return in_array(get_class($action), $config, true);
    }

    /**
     * Returns configuration for given content type identifier if exists
     * or default one.
     *
     * @param string $contentTypeIdentifier
     *
     * @return array
     */
    protected function prepareConfig($contentTypeIdentifier): array
    {
        if (!empty($this->config['content_types'][$contentTypeIdentifier])) {
            return $this->config['content_types'][$contentTypeIdentifier];
        }

        if (!empty($this->config['default'])) {
            return $this->config['default'];
        }

        return [];
    }
}
