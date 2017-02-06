<?php

namespace Netgen\Bundle\InformationCollectionBundle\Action;

use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;
use Netgen\Bundle\InformationCollectionBundle\Exception\ActionFailedException;
use Psr\Log\LoggerInterface;

class ActionRegistry
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $actions;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * ActionAggregate constructor.
     *
     * @param array $config
     * @param LoggerInterface $logger
     */
    public function __construct($config, LoggerInterface $logger)
    {
        $this->config = $config;
        $this->actions = [];
        $this->logger = $logger;
    }

    /**
     * Adds action to stack
     *
     * @param string $name
     * @param ActionInterface $action
     */
    public function addAction($name, ActionInterface $action)
    {
        $this->actions[$name] = $action;
    }

    /**
     * @inheritDoc
     */
    public function act(InformationCollected $event)
    {
        $config = $this->prepareConfig($event->getContentType()->identifier);

        /** @var ActionInterface $action */
        foreach ($this->actions as $name => $action) {
            if ($this->canActionAct($name, $config)) {

                try {
                    $action->act($event);
                } catch(ActionFailedException $e) {
                    $this->logger->error(
                        sprintf("Error occured while acting on action %s.", $name)
                    );
                }
            }
        }
    }

    /**
     * Check if given action can act
     *
     * @param string $name
     * @param array $config
     *
     * @return bool
     */
    protected function canActionAct($name, array $config)
    {
        return in_array($name, $config, true);
    }

    /**
     * Returns configuration for given content type identifier if exists
     * or default one
     *
     * @param string $contentTypeIdentifier
     *
     * @return array
     */
    protected function prepareConfig($contentTypeIdentifier)
    {
        if (!empty($this->config['content_type'][$contentTypeIdentifier])) {
            return $this->config['content_type'][$contentTypeIdentifier];
        }

        if (!empty($this->config['default'])) {
            return  $this->config['default'];
        }

        return [];
    }
}
