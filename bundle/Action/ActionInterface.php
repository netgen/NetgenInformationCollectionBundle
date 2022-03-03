<?php

namespace Netgen\Bundle\InformationCollectionBundle\Action;

use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollectedInterface;

interface ActionInterface
{
    /**
     * Act on InformationCollected event.
     *
     * @param \Netgen\Bundle\InformationCollectionBundle\Event\InformationCollectedInterface $event
     *
     * @throws \Netgen\Bundle\InformationCollectionBundle\Exception\ActionFailedException
     */
    public function act(InformationCollectedInterface $event);
}
