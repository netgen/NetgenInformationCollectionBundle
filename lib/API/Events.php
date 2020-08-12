<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API;

final class Events
{
    /**
     * The INFORMATION_COLLECTED event occurs just after the information collection has been submitted.
     *
     * The event listener method receives a \Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected
     */
    public const INFORMATION_COLLECTED = 'netgen_information_collection.events.information_collected';
}

