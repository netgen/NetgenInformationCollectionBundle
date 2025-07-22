<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Service;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Netgen\InformationCollection\API\Exception\PersistingFailedException;
use Netgen\InformationCollection\API\Exception\StoringAttributeFailedException;
use Netgen\InformationCollection\API\Exception\StoringCollectionFailedException;
use Netgen\InformationCollection\API\Factory\FieldValueFactoryInterface;
use Netgen\InformationCollection\API\Service\InformationCollection;
use Netgen\InformationCollection\API\Value\Attribute;
use Netgen\InformationCollection\API\Value\Collection;
use Netgen\InformationCollection\API\Value\CollectionCount;
use Netgen\InformationCollection\API\Value\Collections;
use Netgen\InformationCollection\API\Value\ContentsWithCollections;
use Netgen\InformationCollection\API\Value\Filter\CollectionFields;
use Netgen\InformationCollection\API\Value\Filter\CollectionId;
use Netgen\InformationCollection\API\Value\Filter\Collections as FilterCollections;
use Netgen\InformationCollection\API\Value\Filter\ContentId;
use Netgen\InformationCollection\API\Value\Filter\Contents;
use Netgen\InformationCollection\API\Value\Filter\FilterCriteria;
use Netgen\InformationCollection\API\Value\Filter\Query;
use Netgen\InformationCollection\API\Value\Filter\SearchCountQuery;
use Netgen\InformationCollection\API\Value\Filter\SearchQuery;
use Netgen\InformationCollection\API\Value\InformationCollectionStruct;
use Netgen\InformationCollection\API\Value\ObjectCount;
use Netgen\InformationCollection\API\Value\SearchCount;
use Netgen\InformationCollection\Core\Mapper\DomainObjectMapper;
use Netgen\InformationCollection\Core\Persistence\Gateway\DoctrineDatabase;
use Netgen\InformationCollection\Doctrine\Repository\EzInfoCollectionAttributeRepository;
use Netgen\InformationCollection\Doctrine\Repository\EzInfoCollectionRepository;
use function count;

class InformationCollectionService implements InformationCollection
{
    protected EzInfoCollectionRepository $ezInfoCollectionRepository;

    protected EzInfoCollectionAttributeRepository $ezInfoCollectionAttributeRepository;

    protected Repository $repository;

    protected DoctrineDatabase $gateway;

    protected FieldValueFactoryInterface $fieldsFactory;

    protected DomainObjectMapper $objectMapper;

    public function __construct(
        EzInfoCollectionRepository $ezInfoCollectionRepository,
        EzInfoCollectionAttributeRepository $ezInfoCollectionAttributeRepository,
        Repository $repository,
        DoctrineDatabase $gateway,
        FieldValueFactoryInterface $factory,
        DomainObjectMapper $objectMapper
    ) {
        $this->ezInfoCollectionRepository = $ezInfoCollectionRepository;
        $this->ezInfoCollectionAttributeRepository = $ezInfoCollectionAttributeRepository;
        $this->repository = $repository;
        $this->gateway = $gateway;
        $this->fieldsFactory = $factory;
        $this->objectMapper = $objectMapper;
    }

    public function createCollection(InformationCollectionStruct $struct): void
    {
        $contentType = $struct->getContentType();
        $content = $struct->getContent();

        $userReference = $this->repository
            ->getPermissionResolver()
            ->getCurrentUserReference();

        $user = $this->getUser(
            $userReference->getUserId()
        );

        $ezInfo = $this->ezInfoCollectionRepository
            ->createNewFromValues($content, $user);

        try {
            $this->ezInfoCollectionRepository->save($ezInfo);
        } catch (StoringCollectionFailedException $e) {
            throw new PersistingFailedException('collection', $e->getMessage());
        }

        foreach ($struct->getFieldsData() as $fieldDefIdentifier => $value) {
            if ($value->value === null) {
                continue;
            }

            $value = $this->fieldsFactory->getLegacyValue($value->value, $contentType->getFieldDefinition($fieldDefIdentifier));
            $ezInfoAttribute = $this->ezInfoCollectionAttributeRepository
                ->createNewFromValues($content, $ezInfo, $value, $fieldDefIdentifier);

            try {
                $this->ezInfoCollectionAttributeRepository->save($ezInfoAttribute);
            } catch (StoringAttributeFailedException $e) {
                throw new PersistingFailedException('attribute', $e->getMessage());
            }
        }
    }

    public function getObjectsWithCollectionsCount(): ObjectCount
    {
        return new ObjectCount(
            $this->gateway->getContentsWithCollectionsCount()
        );
    }

    public function getObjectsWithCollections(Query $query): ContentsWithCollections
    {
        $objects = $this->gateway->getObjectsWithCollections($query->getLimit(), $query->getOffset());

        $contents = [];
        foreach ($objects as $object) {
            $contentId = (int) $object['content_id'];

            $childCount = $this->ezInfoCollectionRepository->getChildrenCount($contentId);

            $contents[] = $this->objectMapper
                ->mapContent(
                    $object,
                    $this->ezInfoCollectionRepository->getFirstCollection($contentId),
                    $this->ezInfoCollectionRepository->getLastCollection($contentId),
                    $childCount
                );
        }

        return new ContentsWithCollections($contents, count($contents));
    }

    public function getCollectionsCount(ContentId $contentId): CollectionCount
    {
        return new CollectionCount(
            $this->ezInfoCollectionRepository->getChildrenCount($contentId->getContentId())
        );
    }

    public function getCollections(ContentId $contentId): Collections
    {
        $collections = $this->ezInfoCollectionRepository->findBy(
            [
                'contentObjectId' => $contentId->getContentId(),
            ],
            [],
            $contentId->getLimit(),
            $contentId->getOffset()
        );

        $objects = [];
        foreach ($collections as $collection) {
            $objects[] = $this->loadCollection($collection->getId());
        }

        return new Collections($objects, count($objects));
    }

    public function filterCollections(FilterCriteria $criteria): Collections
    {
        $collections = $this->ezInfoCollectionRepository->filterByIntervalOfCreation(
            $criteria->getContentId()->getContentId(),
            $criteria->getFrom(),
            $criteria->getTo(),
            $criteria->getContentId()->getLimit(),
            $criteria->getContentId()->getOffset()
        );

        $objects = [];
        foreach ($collections as $collection) {
            $objects[] = $this->loadCollection($collection['id']);
        }

        return new Collections($objects, count($objects));
    }

    public function searchCount(SearchCountQuery $query): SearchCount
    {
        $collections = $this->ezInfoCollectionAttributeRepository
            ->search($query->getContentId(), $query->getSearchText());

        // needs rewrite
        $collections = $this->ezInfoCollectionRepository->findBy(
            [
                'id' => $collections,
            ]
        );

        return new SearchCount(count($collections));
    }

    public function search(SearchQuery $query): Collections
    {
        $collections = $this->ezInfoCollectionAttributeRepository
            ->search($query->getContentId(), $query->getSearchText());

        $collections = $this->ezInfoCollectionRepository->findBy(
            [
                'id' => $collections,
            ],
            [],
            $query->getLimit(),
            $query->getOffset()
        );

        $objects = [];
        foreach ($collections as $collection) {
            $objects[] = $this->loadCollection($collection->getId());
        }

        return new Collections($objects, count($objects));
    }

    public function getCollection(CollectionId $collectionId): Collection
    {
        return $this->loadCollection($collectionId->getCollectionId());
    }

    public function deleteCollectionFields(CollectionFields $collectionFields): void
    {
        $attributes = $this->ezInfoCollectionAttributeRepository
            ->findBy(
                [
                    'contentObjectId' => $collectionFields->getContentId(),
                    'informationCollectionId' => $collectionFields->getCollectionId(),
                    'contentClassAttributeId' => $collectionFields->getFields(),
                ]
            );

        $this->ezInfoCollectionAttributeRepository->remove($attributes);
    }

    public function deleteCollections(FilterCollections $collections): void
    {
        $collections = $this->ezInfoCollectionRepository
            ->findBy([
                'contentObjectId' => $collections->getContentId(),
                'id' => $collections->getCollectionIds(),
            ]);

        foreach ($collections as $collection) {
            $attributes = $this->ezInfoCollectionAttributeRepository->findBy(['informationCollectionId' => $collection->getId()]);
            $this->ezInfoCollectionAttributeRepository->remove($attributes);
        }

        $this->ezInfoCollectionRepository->remove($collections);
    }

    public function deleteCollectionByContent(Contents $contents): void
    {
        $collections = $this->ezInfoCollectionRepository
            ->findBy([
                'contentObjectId' => $contents->getContentIds(),
            ]);

        foreach ($collections as $collection) {
            $attributes = $this->ezInfoCollectionAttributeRepository->findBy(['informationCollectionId' => $collection->getId()]);
            $this->ezInfoCollectionAttributeRepository->remove($attributes);
        }

        $this->ezInfoCollectionRepository->remove($collections);
    }

    public function updateCollectionAttribute(CollectionId $collectionId, Attribute $attribute): void
    {
        $this->ezInfoCollectionAttributeRepository->updateByCollectionId($collectionId, $attribute);
    }

    protected function getUser(int $userId): User
    {
        try {
            return $this->repository
                ->getUserService()
                ->loadUser($userId);
        } catch (NotFoundException $exception) {
        }
    }

    protected function loadCollection(int $collectionId): Collection
    {
        $collection = $this->ezInfoCollectionRepository->loadCollection($collectionId);
        $attributes = $this->ezInfoCollectionAttributeRepository->findBy(
            [
                'informationCollectionId' => $collectionId,
            ]
        );

        return $this->objectMapper->mapCollection($collection, $attributes);
    }
}
