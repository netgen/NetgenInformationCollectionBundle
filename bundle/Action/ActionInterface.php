<?php

namespace Netgen\Bundle\InformationCollectionBundle\Action;

use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;
use Netgen\Bundle\InformationCollectionBundle\Exception\ActionFailedException;

interface ActionInterface
{
    /**
     * Act on InformationCollected event
     *
     * @param InformationCollected $event
     *
     * @throws ActionFailedException
     */
    public function act(InformationCollected $event);
}
