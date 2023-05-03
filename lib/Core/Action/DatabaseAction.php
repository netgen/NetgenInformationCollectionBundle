<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Action;

use Netgen\InformationCollection\API\Action\ActionInterface;
use Netgen\InformationCollection\API\Action\CrucialActionInterface;
use Netgen\InformationCollection\API\Exception\ActionFailedException;
use Netgen\InformationCollection\API\Exception\PersistingFailedException;
use Netgen\InformationCollection\API\Service\InformationCollection;
use Netgen\InformationCollection\API\Value\Event\InformationCollected;

final class DatabaseAction implements ActionInterface, CrucialActionInterface
{
    public static string $defaultName = 'database';

    private InformationCollection $informationCollection;

    public function __construct(InformationCollection $informationCollection)
    {
        $this->informationCollection = $informationCollection;
    }

    public function act(InformationCollected $event): void
    {
        $struct = $event->getInformationCollectionStruct();

        try {
            $this->informationCollection
                ->createCollection($struct);
        } catch (PersistingFailedException $e) {
            throw new ActionFailedException(static::$defaultName, $e->getMessage());
        }
    }
}
