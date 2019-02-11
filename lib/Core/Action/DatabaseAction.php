<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Action;

use Doctrine\DBAL\DBALException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\Core\Repository\Values\Content\Content;
use Netgen\InformationCollection\Doctrine\Entity\EzInfoCollection;
use Netgen\InformationCollection\API\Value\Event\InformationCollected;
use Netgen\InformationCollection\API\Exception\ActionFailedException;
use Netgen\InformationCollection\Core\Factory\FieldDataFactory;
use Netgen\InformationCollection\Doctrine\Repository\EzInfoCollectionAttributeRepository;
use Netgen\InformationCollection\Doctrine\Repository\EzInfoCollectionRepository;

use Netgen\InformationCollection\API\Action\ActionInterface;
use Netgen\InformationCollection\API\Action\CrucialActionInterface;
use Netgen\InformationCollection\API\Value\Legacy\FieldValue;

class DatabaseAction implements ActionInterface, CrucialActionInterface
{
    /**
     * @var FieldDataFactory
     */
    protected $factory;

    /**
     * @var EzInfoCollectionRepository
     */
    protected $infoCollectionRepository;

    /**
     * @var EzInfoCollectionAttributeRepository
     */
    protected $infoCollectionAttributeRepository;

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * PersistToDatabaseAction constructor.
     *
     * @param FieldDataFactory $factory
     * @param EzInfoCollectionRepository $infoCollectionRepository
     * @param EzInfoCollectionAttributeRepository $infoCollectionAttributeRepository
     * @param Repository $repository
     */
    public function __construct(
        FieldDataFactory $factory,
        EzInfoCollectionRepository $infoCollectionRepository,
        EzInfoCollectionAttributeRepository $infoCollectionAttributeRepository,
        Repository $repository
    ) {
        $this->factory = $factory;
        $this->infoCollectionRepository = $infoCollectionRepository;
        $this->infoCollectionAttributeRepository = $infoCollectionAttributeRepository;
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function act(InformationCollected $event): void
    {
        $struct = $event->getInformationCollectionStruct();
        $contentType = $event->getContentType();
        $location = $event->getLocation();

        /** @var Content $content */
        $content = $this->repository->getContentService()->loadContent($location->contentInfo->id);

        $currentUser = $this->repository->getCurrentUser();
        $dt = new \DateTimeImmutable();

        /** @var EzInfoCollection $ezInfo */
        $ezInfo = $this->infoCollectionRepository->getInstance();

        $ezInfo->setContentObjectId($location->getContentInfo()->id);
        $ezInfo->setUserIdentifier($currentUser->login);
        $ezInfo->setCreatorId($currentUser->id);
        $ezInfo->setCreated($dt->getTimestamp());
        $ezInfo->setModified($dt->getTimestamp());

        try {
            $this->infoCollectionRepository->save($ezInfo);
        } catch (DBALException $e) {
            throw new ActionFailedException('database', $e->getMessage());
        }

        /**
         * @var string
         * @var \eZ\Publish\Core\FieldType\Value $value
         */
        foreach ($struct->getCollectedFields() as $fieldDefIdentifier => $value) {
            if ($value === null) {
                continue;
            }

            $value = $this->factory->getLegacyValue($value, $contentType->getFieldDefinition($fieldDefIdentifier));

            $ezInfoAttribute = $this->infoCollectionAttributeRepository->getInstance();

            /* @var LegacyData $value */
            $ezInfoAttribute->setContentObjectId($location->getContentInfo()->id);
            $ezInfoAttribute->setInformationCollectionId($ezInfo->getId());
            $ezInfoAttribute->setContentClassAttributeId($value->getContentClassAttributeId());
            $ezInfoAttribute->setContentObjectAttributeId($content->getField($fieldDefIdentifier)->id);
            $ezInfoAttribute->setDataInt($value->getDataInt());
            $ezInfoAttribute->setDataFloat($value->getDataFloat());
            $ezInfoAttribute->setDataText($value->getDataText());

            try {
                $this->infoCollectionAttributeRepository->save($ezInfoAttribute);
            } catch (DBALException $e) {
                throw new ActionFailedException('database', $e->getMessage());
            }
        }
    }
}
