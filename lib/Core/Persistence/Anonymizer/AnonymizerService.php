<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Persistence\Anonymizer;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use Netgen\InformationCollection\API\Persistence\Anonymizer\Anonymizer;
use Netgen\InformationCollection\API\Persistence\Anonymizer\Visitor\FieldAnonymizerVisitor;
use Netgen\InformationCollection\API\Service\InformationCollection;
use Netgen\InformationCollection\API\Value\Collection;
use Netgen\InformationCollection\API\Value\Filter\CollectionId;
use Netgen\InformationCollection\API\Value\Attribute;

class AnonymizerService implements Anonymizer
{
    /**
     * @var \eZ\Publish\API\Repository\Repository
     */
    protected $repository;

    /**
     * @var \Netgen\InformationCollection\API\Persistence\Anonymizer\Visitor\FieldAnonymizerVisitor
     */
    protected $fieldAnonymizerVisitor;

    /**
     * @var \Netgen\InformationCollection\API\Service\InformationCollection
     */
    protected $informationCollection;

    /**
     * Anonymizer constructor.
     *
     * @param \eZ\Publish\API\Repository\Repository $repository
     * @param \Netgen\InformationCollection\API\Service\InformationCollection $informationCollection
     * @param \Netgen\InformationCollection\API\Persistence\Anonymizer\Visitor\FieldAnonymizerVisitor $fieldAnonymizerVisitor
     */
    public function __construct(
        Repository $repository,
        InformationCollection $informationCollection,
        FieldAnonymizerVisitor $fieldAnonymizerVisitor
    ) {
        $this->informationCollection = $informationCollection;
        $this->repository = $repository;
        $this->fieldAnonymizerVisitor = $fieldAnonymizerVisitor;
    }

    public function anonymizeCollection(int $collectionId, array $fields = []): void
    {
        $collectionId = new CollectionId($collectionId);
        $collection = $this->informationCollection->getCollection($collectionId);

        $this->destroyData($collection, $fields);
    }

    /**
     * @param \Netgen\InformationCollection\API\Value\Collection $collection
     * @param array $fields
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    protected function destroyData(Collection $collection, array $fields = [])
    {
        $contentType = $this->repository
            ->getContentTypeService()
            ->loadContentType($collection->getContent()->contentInfo->contentTypeId);

        $attributes = $this->filterAttributes($collection, $fields);

        if (empty($attributes)) {
            throw new \OutOfRangeException("The is no valid fields selected for anonymization");
        }

        $collectionId = new CollectionId($collection->getId());
        $this->anonymize($collectionId, $attributes, $contentType);
    }

    /**
     * @param \Netgen\InformationCollection\API\Value\Attribute[] $attributes
     */
    protected function anonymize(CollectionId $collectionId, array $attributes, ContentType $contentType)
    {
        foreach ($attributes as $attribute) {
            $value = $this->fieldAnonymizerVisitor->visit($attribute, $contentType);

            $attributeToUpdate = Attribute::createFromAttributeAndValue($attribute, $value);

            $this->informationCollection->updateCollectionAttribute($collectionId, $attributeToUpdate);
        }
    }

    /**
     * Filter attributes based on the user selection of fields to anonymize
     *
     * @param Collection $collection
     * @param array $fields
     *
     * @return array
     */
    protected function filterAttributes(Collection $collection, array $fields): array
    {
        if (empty($fields)) {
            return $collection->getAttributes();
        }

        $attributes = $collection->getAttributes();

        $filtered = [];
        foreach ($attributes as $attribute) {

            if (in_array($attribute->getFieldDefinition()->id, $fields)) {
                $filtered[] = $attribute;
            }
        }

        return $filtered;
    }
}
