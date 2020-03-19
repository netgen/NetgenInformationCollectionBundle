<?php

namespace Netgen\Bundle\InformationCollectionBundle\Action;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Netgen\Bundle\InformationCollectionBundle\DependencyInjection\ConfigurationConstants;
use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;
use Netgen\Bundle\InformationCollectionBundle\Exception\ActionFailedException;
use Netgen\Bundle\InformationCollectionBundle\Priority;
use Psr\Log\LoggerInterface;
use function in_array;
use function usort;

class ActionRegistry
{
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
     * @var \eZ\Publish\Core\MVC\ConfigResolverInterface
     */
    protected $configResolver;

    /**
     * ActionAggregate constructor.
     *
     * @param \eZ\Publish\Core\MVC\ConfigResolverInterface $configResolver
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(ConfigResolverInterface $configResolver, LoggerInterface $logger)
    {
        $this->actions = array();
        $this->logger = $logger;
        $this->configResolver = $configResolver;
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
        $config = $this->configResolver->getParameter('actions', 'netgen_information_collection');

        if (!empty($config[ConfigurationConstants::CONTENT_TYPES][$contentTypeIdentifier])) {
            return $config[ConfigurationConstants::CONTENT_TYPES][$contentTypeIdentifier];
        }

        if (!empty($config['default'])) {
            return  $config['default'];
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
