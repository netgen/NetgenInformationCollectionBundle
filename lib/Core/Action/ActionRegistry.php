<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Action;

use function in_array;
use Netgen\InformationCollection\API\Action\ActionInterface;
use Netgen\InformationCollection\API\Action\CrucialActionInterface;
use Netgen\InformationCollection\API\Exception\ActionFailedException;
use Netgen\InformationCollection\API\Priority;
use Netgen\InformationCollection\API\Value\Event\InformationCollected;
use Psr\Log\LoggerInterface;
use function usort;

class ActionRegistry implements ActionInterface
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
    public function __construct(array $config, LoggerInterface $logger)
    {
        $this->config = $config;
        $this->actions = [];
        $this->logger = $logger;
    }

    /**
     * Adds action to stack.
     *
     * @param string $name
     * @param ActionInterface $action
     * @param int $priority
     */
    public function addAction(string $name, ActionInterface $action, int $priority = Priority::DEFAULT_PRIORITY): void
    {
        $this->actions[] = [
            'name' => $name,
            'action' => $action,
            'priority' => $priority,
        ];
    }

    public function act(InformationCollected $event): void
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
        if (!empty($this->config['content_types'][$contentTypeIdentifier])) {
            return $this->config['content_types'][$contentTypeIdentifier];
        }

        if (!empty($this->config['default'])) {
            return  $this->config['default'];
        }

        return [];
    }

    /**
     * Sorts actions by priority.
     */
    protected function prepareActions()
    {
        usort($this->actions, static function ($one, $two) {
            if ($one['priority'] === $two['priority']) {
                return 0;
            }

            return ($one['priority'] > $two['priority']) ? -1 : 1;
        });
    }
}
