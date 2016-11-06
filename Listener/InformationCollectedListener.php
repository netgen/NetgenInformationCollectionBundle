<?php

namespace Netgen\Bundle\InformationCollectionBundle\Listener;

use Netgen\Bundle\InformationCollectionBundle\Action\ActionAggregate;
use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;
use Netgen\Bundle\InformationCollectionBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InformationCollectedListener implements EventSubscriberInterface
{
    /**
     * @var ActionAggregate
     */
    protected $actionAggregate;

    /**
     * InformationCollectedListener constructor.
     *
     * @param ActionAggregate $actionAggregate
     */
    public function __construct(ActionAggregate $actionAggregate)
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