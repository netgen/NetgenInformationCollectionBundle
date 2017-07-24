<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Action;

use Netgen\Bundle\InformationCollectionBundle\Action\ActionInterface;
use Netgen\Bundle\InformationCollectionBundle\Action\CrucialActionInterface;
use Netgen\Bundle\InformationCollectionBundle\Exception\ActionFailedException;

class CrucialActionStub implements ActionInterface, CrucialActionInterface
{
    public function act(\Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected $event)
    {
        throw new ActionFailedException("crucial", "test");
    }
}
