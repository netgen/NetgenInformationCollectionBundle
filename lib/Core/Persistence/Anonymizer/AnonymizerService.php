<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Persistence\Anonymizer;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use Netgen\InformationCollection\API\Persistence\Anonymizer\Anonymizer;
use Netgen\InformationCollection\API\Persistence\Anonymizer\Visitor\FieldAnonymizerVisitor;
use Netgen\InformationCollection\Doctrine\Entity\EzInfoCollection;
use Netgen\InformationCollection\Doctrine\Repository\EzInfoCollectionRepository;
use Netgen\InformationCollection\Doctrine\Repository\EzInfoCollectionAttributeRepository;

class AnonymizerService implements Anonymizer
{
    /**
     * @var \Netgen\InformationCollection\Doctrine\Repository\EzInfoCollectionRepository
     */
    protected $collectionRepository;

    /**
     * @var \Netgen\InformationCollection\Doctrine\Repository\EzInfoCollectionAttributeRepository
     */
    protected $collectionAttributeRepository;

    /**
     * @var \eZ\Publish\API\Repository\Repository
     */
    protected $repository;

    /**
     * @var \Netgen\InformationCollection\API\Persistence\Anonymizer\Visitor\FieldAnonymizerVisitor
     */
    protected $fieldAnonymizerVisitor;

    /**
     * Anonymizer constructor.
     *
     * @param \eZ\Publish\API\Repository\Repository $repository
     * @param \Netgen\InformationCollection\Doctrine\Repository\EzInfoCollectionRepository $collectionRepository
     * @param \Netgen\InformationCollection\Doctrine\Repository\EzInfoCollectionAttributeRepository $collectionAttributeRepository
     * @param \Netgen\InformationCollection\API\Persistence\Anonymizer\Visitor\FieldAnonymizerVisitor $fieldAnonymizerVisitor
     */
    public function __construct(
        Repository $repository,
        EzInfoCollectionRepository $collectionRepository,
        EzInfoCollectionAttributeRepository $collectionAttributeRepository,
        FieldAnonymizerVisitor $fieldAnonymizerVisitor
    ) {
        $this->collectionRepository = $collectionRepository;
        $this->collectionAttributeRepository = $collectionAttributeRepository;
        $this->repository = $repository;
        $this->fieldAnonymizerVisitor = $fieldAnonymizerVisitor;
    }

    public function anonymizeCollection($collectionId, array $fields = []): void
    {
        $collection = $this->collectionRepository->find($collectionId);

        if (!$collection instanceof EzInfoCollection) {
            return;
        }

        $this->destroyData($collection, $fields);
    }

    /**
     * @param \Netgen\InformationCollection\Doctrine\Entity\EzInfoCollection $collection
     * @param array $fields
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    protected function destroyData(EzInfoCollection $collection, array $fields = [])
    {
        $content = $this->repository
            ->getContentService()
            ->loadContent($collection->getContentObjectId());

        $contentType = $this->repository
            ->getContentTypeService()
            ->loadContentType($content->contentInfo->contentTypeId);

        $query = [
            'informationCollectionId' => $collection->getId(),
        ];

        if (!empty($fields)) {
            $query['contentClassAttributeId'] = $fields;
        }

        $attributes = $this->collectionAttributeRepository
            ->findBy($query);

        $this->anonymize($attributes, $contentType);
    }

    /**
     * @param \Netgen\InformationCollection\Doctrine\Entity\EzInfoCollectionAttribute[] $attributes
     */
    protected function anonymize(array $attributes, ContentType $contentType)
    {
        foreach ($attributes as $attribute) {
            $value = $this->fieldAnonymizerVisitor->visit($attribute, $contentType);
            $attribute->setDataText($value);

            $this->collectionAttributeRepository->save($attribute);
        }
    }
}
