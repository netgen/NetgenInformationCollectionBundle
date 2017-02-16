<?php

namespace Netgen\Bundle\InformationCollectionBundle\Action;

use Doctrine\DBAL\DBALException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\Core\Repository\Values\Content\Content;
use Netgen\Bundle\InformationCollectionBundle\Entity\EzInfoCollection;
use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;
use Netgen\Bundle\InformationCollectionBundle\Exception\ActionFailedException;
use Netgen\Bundle\InformationCollectionBundle\Factory\FieldDataFactory;
use Netgen\Bundle\InformationCollectionBundle\Repository\EzInfoCollectionAttributeRepository;
use Netgen\Bundle\InformationCollectionBundle\Repository\EzInfoCollectionRepository;
use Netgen\Bundle\InformationCollectionBundle\Value\LegacyData;

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
     * @inheritDoc
     */
    public function act(InformationCollected $event)
    {
        $struct = $event->getInformationCollectionStruct();
        $contentType = $event->getContentType();
        $location = $event->getLocation();

        /** @var Content $content */
        $content = $this->repository->getContentService()->loadContent($location->contentInfo->id);

        $currentUser = $this->repository->getCurrentUser();
        $dt = new \DateTime();

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
         * @var string $fieldDefIdentifier
         * @var \eZ\Publish\Core\FieldType\Value $value
         */
        foreach ($struct->getCollectedFields() as $fieldDefIdentifier => $value) {

            $value = $this->factory->getLegacyValue($value, $contentType->getFieldDefinition($fieldDefIdentifier));

            $ezInfoAttribute = $this->infoCollectionAttributeRepository->getInstance();

            /** @var LegacyData $value */
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
