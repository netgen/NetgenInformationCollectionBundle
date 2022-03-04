<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Listener;

use Netgen\InformationCollection\API\Events;
use Netgen\InformationCollection\API\Value\Event\InformationCollected;
use Netgen\InformationCollection\Core\Action\ActionRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InformationCollectedListener implements EventSubscriberInterface
{
    /**
     * @var ActionRegistry
     */
    protected $actionAggregate;

    /**
     * @param ActionRegistry $actionAggregate
     */
    public function __construct(ActionRegistry $actionAggregate)
    {
        $this->actionAggregate = $actionAggregate;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Events::INFORMATION_COLLECTED => 'onInformationCollected',
        ];
    }

    /**
     * Run all actions.
     *
     * @param InformationCollected $event
     */
    public function onInformationCollected(InformationCollected $event): void
    {
        $this->actionAggregate->act($event);
    }
}
