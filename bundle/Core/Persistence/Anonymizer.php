<?php

namespace Netgen\Bundle\InformationCollectionBundle\Core\Persistence;

use Netgen\Bundle\InformationCollectionBundle\API\Persistence\Anonymizer as AnonymizerAPI;
use eZ\Publish\API\Repository\Repository;
use Netgen\Bundle\InformationCollectionBundle\Entity\EzInfoCollection;
use Netgen\Bundle\InformationCollectionBundle\Entity\EzInfoCollectionAttribute;
use Netgen\Bundle\InformationCollectionBundle\Repository\EzInfoCollectionAttributeRepository;
use Netgen\Bundle\InformationCollectionBundle\Repository\EzInfoCollectionRepository;

class Anonymizer implements AnonymizerAPI
{
    /**
     * @var EzInfoCollectionRepository
     */
    protected $collectionRepository;

    /**
     * @var EzInfoCollectionAttributeRepository
     */
    protected $collectionAttributeRepository;

    /**
     * @var Repository
     */
    protected $repository;

    public function __construct(Repository $repository, EzInfoCollectionRepository $collectionRepository, EzInfoCollectionAttributeRepository $collectionAttributeRepository)
    {
        $this->collectionRepository = $collectionRepository;
        $this->collectionAttributeRepository = $collectionAttributeRepository;
        $this->repository = $repository;
    }

    public function anonymizeByContentId($contentId)
    {
        /** @var EzInfoCollection $collection */
        $collection = $this->collectionRepository->findOlderThanThirtyDaysByContentId($contentId);

        $this->destroyData($collection);
    }

    public function anonymizeByContentIdAndFieldList($contentId, $fields)
    {
        $fieldDefinitionIds = $this->getFieldDefinitionIds($contentId, $fields);

        $collections = $this->collectionRepository->findOlderThanThirtyDaysByContentId($contentId);

        /** @var EzInfoCollection $collection */
        foreach ($collections as $collection) {

            $this->destroyData($collection, $fieldDefinitionIds);

        }
    }

    public function anonymizeByCollectedInfoId($collectedInfoId)
    {
        /** @var EzInfoCollection $collection */
        $collection = $this->collectionRepository->findOlderThanThirtyDaysById($collectedInfoId);

        $fieldDefinitionIds = $this->getFieldDefinitionIds($collection->getContentObjectId(), $fields);


        $this->destroyData($collection, $fieldDefinitionIds);
    }

    public function anonymizeByCollectedInfoIdAndFieldList($collectedInfoId, $fields)
    {
        /** @var EzInfoCollection $collection */
        $collection = $this->collectionRepository->findOlderThanThirtyDaysById($collectedInfoId);

        $fieldDefinitionIds = $this->getFieldDefinitionIds($collection->getContentObjectId(), $fields);


        $this->destroyData($collection);
    }

    protected function getFieldDefinitionIds($contentId, $fields)
    {
        $fieldDefinitionIds = [];

        $content = $this->repository->getContentService()
            ->loadContent($contentId);

        $contentType = $this->repository->getContentTypeService()
            ->loadContentType($content->contentInfo->contentTypeId);

        foreach ($fields as $field) {
            $fieldDefinitionIds[] = $contentType->getFieldDefinition($field)->id;
        }

        return $fieldDefinitionIds;
    }

    protected function destroyDataForFields(EzInfoCollection $collection, $fieldDefinitionIds)
    {
        $attributes = $this->collectionAttributeRepository->findByCollectionIdAndFieldDefinitionIds($collection->getId(), $fieldDefinitionIds);

        /** @var EzInfoCollectionAttribute $attribute */
        foreach ($attributes as $attribute) {
            $attribute->setDataText("XXXXXXXXXX");

            $this->collectionAttributeRepository->save($attribute);
        }
    }

    protected function destroyData(EzInfoCollection $collection)
    {
        $attributes = $this->collectionAttributeRepository->findByCollectionId($collection->getId());

        /** @var EzInfoCollectionAttribute $attribute */
        foreach ($attributes as $attribute) {
            $attribute->setDataText("XXXXXXXXXX");

            $this->collectionAttributeRepository->save($attribute);
        }
    }
}
