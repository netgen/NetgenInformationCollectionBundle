<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Action;

use Netgen\InformationCollection\API\Value\Event\InformationCollected;

interface ActionInterface
{
    /**
     * Act on InformationCollected event.
     *
     * @param \Netgen\InformationCollection\API\Value\Event\InformationCollected $event
     *
     * @throws \Netgen\InformationCollection\API\Exception\ActionFailedException
     */
    public function act(InformationCollected $event): void;
}
