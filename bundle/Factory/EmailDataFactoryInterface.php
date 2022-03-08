<?php

namespace Netgen\Bundle\InformationCollectionBundle\Factory;

use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;
use Netgen\Bundle\InformationCollectionBundle\Value\EmailData;

interface EmailDataFactoryInterface
{
    /**
     * Factory method.
     *
     * @param InformationCollected $value
     *
     * @return EmailData
     */
    public function build(InformationCollected $value);
}
