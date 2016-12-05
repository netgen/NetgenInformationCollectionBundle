<?php

namespace Netgen\Bundle\InformationCollectionBundle\Action;

use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;

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
     * ActionAggregate constructor.
     *
     * @param array $config
     */
    public function __construct($config)
    {
        $this->config = $config;
        $this->actions = [];
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
        /** @var ActionInterface $action */
        foreach ($this->actions as $name => $action) {

            $config = $this->getConfigForContentType($event->getContentType()->identifier);

            if (empty($config)) {
                continue;
            }

            if ($this->canActionAct($name, $config)) {
                $action->act($event);
            }
        }
    }

    /**
     * Returns configuration for given content type identifier
     *
     * @param $contentTypeIdentifier
     *
     * @return mixed|null
     */
    protected function getConfigForContentType($contentTypeIdentifier)
    {
        if (array_key_exists($contentTypeIdentifier, $this->config)) {
            return $this->config[$contentTypeIdentifier];
        }

        return null;
    }

    /**
     * Check if given action can act
     *
     * @param $action
     * @param array $config
     *
     * @return bool
     */
    protected function canActionAct($action, array $config)
    {
        foreach ($config as $actionConfig) {
            if ($actionConfig['action'] === $action) {
                return true;
            }
        }

        return false;
    }
}