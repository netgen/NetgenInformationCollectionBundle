<?php

namespace Netgen\Bundle\InformationCollectionBundle\Action;

use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;

class ActionAggregate implements ActionInterface
{
    /**
     * @var array
     */
    protected $actions;

    /**
     * ActionAggregate constructor.
     *
     * @param array $actions
     */
    public function __construct(array $actions = [])
    {
        $this->actions = $actions;
    }

    /**
     * Adds action to stack
     *
     * @param ActionInterface $action
     */
    public function addAction(ActionInterface $action)
    {
        $this->actions[] = $action;
    }

    /**
     * @inheritDoc
     */
    public function act(InformationCollected $event)
    {
        foreach ($this->actions as $action) {
            $action->act($event);
        }
    }
}