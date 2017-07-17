<?php

namespace Netgen\Bundle\InformationCollectionBundle\Action;

use Doctrine\DBAL\DBALException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\User\User;
use eZ\Publish\Core\Repository\Values\Content\Content;
use Netgen\Bundle\InformationCollectionBundle\Entity\EzInfoCollection;
use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;
use Netgen\Bundle\InformationCollectionBundle\Exception\ActionFailedException;
use Netgen\Bundle\InformationCollectionBundle\Factory\FieldDataFactory;
use Netgen\Bundle\InformationCollectionBundle\Repository\RepositoryAggregate;
use Netgen\Bundle\InformationCollectionBundle\Value\LegacyData;

class DatabaseAction implements ActionInterface, CrucialActionInterface
{
    /**
     * @var FieldDataFactory
     */
    protected $factory;

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @var RepositoryAggregate
     */
    protected $repositoryAggregate;

    /**
     * PersistToDatabaseAction constructor.
     *
     * @param FieldDataFactory $factory
     * @param RepositoryAggregate $repositoryAggregate
     * @param Repository $repository
     */
    public function __construct(
        FieldDataFactory $factory,
        RepositoryAggregate $repositoryAggregate,
        Repository $repository
    ) {
        $this->factory = $factory;
        $this->repository = $repository;
        $this->repositoryAggregate = $repositoryAggregate;
    }

    /**
     * {@inheritdoc}
     */
    public function act(InformationCollected $event)
    {
        $struct = $event->getInformationCollectionStruct();
        $contentType = $event->getContentType();
        $location = $event->getLocation();

        /** @var Content $content */
        $content = $this->repository->getContentService()->loadContent($location->contentInfo->id);

        /** @var User $currentUser */
        $currentUser = $this->repository->getCurrentUser();

        try {
            /** @var EzInfoCollection $ezInfo */
            $ezInfo = $this->repositoryAggregate->createMain($location, $currentUser);
        } catch (DBALException $e) {
            throw new ActionFailedException('database', $e->getMessage());
        }

        foreach ($struct->getCollectedFields() as $fieldDefIdentifier => $value) {
            /** @var LegacyData $value */
            $value = $this->factory->getLegacyValue($value, $contentType->getFieldDefinition($fieldDefIdentifier));

            try {
                $this->repositoryAggregate
                    ->createChild($location, $ezInfo, $content->getField($fieldDefIdentifier)->id, $value);
            } catch (DBALException $e) {
                throw new ActionFailedException('database', $e->getMessage());
            }
        }
    }
}
