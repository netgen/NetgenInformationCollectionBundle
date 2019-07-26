<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Action;

use Netgen\InformationCollection\API\Action\ActionInterface;
use Netgen\InformationCollection\API\Action\CrucialActionInterface;
use Netgen\InformationCollection\API\Exception\ActionFailedException;
use Netgen\InformationCollection\API\Exception\PersistingFailedException;
use Netgen\InformationCollection\API\Service\InformationCollection;
use Netgen\InformationCollection\API\Value\Event\InformationCollected;

class DatabaseAction implements ActionInterface, CrucialActionInterface
{
    /**
     * @var InformationCollection
     */
    private $informationCollection;

    public function __construct(InformationCollection $informationCollection)
    {
        $this->informationCollection = $informationCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function act(InformationCollected $event): void
    {
        $struct = $event->getInformationCollectionStruct();

        try {
            $this->informationCollection
                ->createCollection($struct);
        } catch (PersistingFailedException $e) {
            throw new ActionFailedException('database', $e->getMessage());
        }
    }
}
