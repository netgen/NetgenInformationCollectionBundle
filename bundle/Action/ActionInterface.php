<?php

namespace Netgen\Bundle\InformationCollectionBundle\Action;

use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;

interface ActionInterface
{
    /**
     * Act on InformationCollected event.
     *
     * @param \Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected $event
     *
     * @throws \Netgen\Bundle\InformationCollectionBundle\Exception\ActionFailedException
     */
    public function act(InformationCollected $event);
}
