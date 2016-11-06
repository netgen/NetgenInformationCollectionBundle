<?php

namespace Netgen\Bundle\InformationCollectionBundle\Action;

use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;

class SendEmailAction implements ActionInterface
{
    /**
     * @inheritDoc
     */
    public function act(InformationCollected $event)
    {
        dump('Email acted');
    }
}