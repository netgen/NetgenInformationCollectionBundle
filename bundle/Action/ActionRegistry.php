<?php

namespace Netgen\Bundle\InformationCollectionBundle\Action;

use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;
use Netgen\Bundle\InformationCollectionBundle\Exception\ActionFailedException;
use Netgen\Bundle\InformationCollectionBundle\Priority;
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
     * @var bool
     */
    protected $debug = false;

    /**
     * ActionAggregate constructor.
     *
     * @param array $config
     * @param LoggerInterface $logger
     */
    public function __construct($config, LoggerInterface $logger)
    {
        $this->config = $config;
        $this->actions = array();
        $this->logger = $logger;
    }

    /**
     * Adds action to stack.
     *
     * @param string $name
     * @param ActionInterface $action
     * @param int $priority
     */
    public function addAction($name, ActionInterface $action, $priority = Priority::DEFAULT_PRIORITY)
    {
        $this->actions[] = array(
            'name' => $name,
            'action' => $action,
            'priority' => $priority,
        );
    }

    public function act(InformationCollected $event)
    {
        $this->prepareActions();
        $config = $this->prepareConfig($event->getContentType()->identifier);

        foreach ($this->actions as $action) {
            if ($this->canActionAct($action['name'], $config)) {
                try {
                    $action['action']->act($event);
                } catch (ActionFailedException $e) {
                    $this->logger
                        ->error($e->getMessage());

                    if ($this->debug) {
                        throw $e;
                    }

                    if ($action['action'] instanceof CrucialActionInterface) {
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
     * or default one.
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

        return array();
    }

    /**
     * Sorts actions by priority.
     */
    protected function prepareActions()
    {
        usort($this->actions, function ($one, $two) {
            if ($one['priority'] === $two['priority']) {
                return 0;
            }

            return ($one['priority'] > $two['priority']) ? -1 : 1;
        });
    }
}
