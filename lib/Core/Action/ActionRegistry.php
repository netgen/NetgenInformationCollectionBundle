<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Action;

use Netgen\InformationCollection\API\Action\ActionInterface;
use Netgen\InformationCollection\API\Action\CrucialActionInterface;
use Netgen\InformationCollection\API\Events;
use Netgen\InformationCollection\API\Exception\ActionFailedException;
use Netgen\InformationCollection\API\Value\Event\InformationCollected;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param array $actions
     * @param \Netgen\InformationCollection\Core\Action\ConfigurationUtility $utility
     */
    public function __construct(iterable $actions, ConfigurationUtility $utility, LoggerInterface $logger, EventDispatcherInterface $eventDispatcher)
    {
        $this->actions = $actions;
        $this->logger = $logger;
        $this->utility = $utility;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function act(InformationCollected $event): void
    {
        $config = $this->utility->getConfigPerContentType($event->getContentType());

        foreach ($this->actions as $action) {
            if ($this->utility->isActionAllowedToRun($action, $config)) {
                $event = $this->eventDispatcher->dispatch($event, Events::BEFORE_ACTION_EXECUTION);

                try {
                    $action->act($event);

                    $event = $this->eventDispatcher->dispatch($event, Events::AFTER_ACTION_EXECUTION);
                } catch (ActionFailedException $e) {
                    $event = $this->eventDispatcher->dispatch($event, Events::ACTION_EXECUTION_FAIL);

                    $this->logger
                        ->error($e->getMessage());

                    if ($this->debug) {
                        throw $e;
                    }

                    if ($action instanceof CrucialActionInterface) {
                        $event = $this->eventDispatcher->dispatch($event, Events::CRUCIAL_ACTION_EXECUTION_FAIL);

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
