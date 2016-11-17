<?php

namespace Netgen\Bundle\InformationCollectionBundle\Listener;

use Netgen\Bundle\InformationCollectionBundle\Action\ActionRegistry;
use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;
use Netgen\Bundle\InformationCollectionBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InformationCollectedListener implements EventSubscriberInterface
{
    /**
     * @var ActionRegistry
     */
    protected $actionAggregate;

    /**
     * InformationCollectedListener constructor.
     *
     * @param ActionRegistry $actionAggregate
     */
    public function __construct(ActionRegistry $actionAggregate)
    {
        $this->actionAggregate = $actionAggregate;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::INFORMATION_COLLECTED => 'onInformationCollected',
        ];
    }

    /**
     * Run all actions
     *
     * @param InformationCollected $event
     */
    public function onInformationCollected(InformationCollected $event)
    {
        $this->actionAggregate->act($event);
    }
}