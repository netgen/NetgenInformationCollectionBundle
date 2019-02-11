<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Action;

interface ActionInterface
{
    /**
     * Act on InformationCollected event.
     *
     * @param \Netgen\InformationCollection\API\Value\Event\InformationCollected $event
     *
     * @throws \Netgen\InformationCollection\API\Exception\ActionFailedException
     */
    public function act(\Netgen\InformationCollection\API\Value\Event\InformationCollected $event): void;
}
