<?php

namespace Netgen\Bundle\InformationCollectionBundle\Action;

interface ActionInterface
{
    /**
     * Act on InformationCollected event.
     *
     * @param \Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected $event
     *
     * @throws \Netgen\Bundle\InformationCollectionBundle\Exception\ActionFailedException
     */
    public function act(\Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected $event);
}
