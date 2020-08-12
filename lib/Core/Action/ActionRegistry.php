<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Action;

use function in_array;
use Netgen\InformationCollection\API\Action\ActionInterface;
use Netgen\InformationCollection\API\Action\CrucialActionInterface;
use Netgen\InformationCollection\API\Exception\ActionFailedException;
use Netgen\InformationCollection\API\Value\Event\InformationCollected;
use Psr\Log\LoggerInterface;
use function get_class;

final class ActionRegistry implements ActionInterface
{
    /**
     * @var array
     */
    private $actions;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var bool
     */
    private $debug = false;

    /**
     * @var \Netgen\InformationCollection\Core\Action\ConfigurationUtility
     */
    private $utility;

    /**
     * ActionAggregate constructor.
     *
     * @param array $actions
     * @param \Netgen\InformationCollection\Core\Action\ConfigurationUtility $utility
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(iterable $actions, ConfigurationUtility $utility, LoggerInterface $logger)
    {
        $this->actions = $actions;
        $this->logger = $logger;
        $this->utility = $utility;
    }

    public function act(InformationCollected $event): void
    {
        $config = $this->utility->getConfigPerContentType($event->getContentType());

        foreach ($this->actions as $action) {
            if ($this->utility->isActionAllowedToRun($action, $config)) {
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
}
