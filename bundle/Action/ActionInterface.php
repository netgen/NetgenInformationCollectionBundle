<?php

namespace Netgen\Bundle\InformationCollectionBundle\Action;

use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;

interface ActionInterface
{
    /**
     * Act on InformationCollected event
     *
     * @param InformationCollected $event
     *
     * @return mixed
     */
    public function act(InformationCollected $event);
}
