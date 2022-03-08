<?php

namespace Netgen\Bundle\InformationCollectionBundle\Factory;

use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollectedInterface;
use Netgen\Bundle\InformationCollectionBundle\Value\EmailData;

interface EmailDataFactoryInterface
{
    /**
     * Factory method.
     *
     * @param InformationCollectedInterface $value
     *
     * @return EmailData
     */
    public function build(InformationCollectedInterface $value): EmailData;
}
