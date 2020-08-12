<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Service;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\User\UserReference;
use Netgen\InformationCollection\API\Exception\PersistingFailedException;
use Netgen\InformationCollection\API\Exception\StoringAttributeFailedException;
use Netgen\InformationCollection\API\Exception\StoringCollectionFailedException;
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
use Netgen\InformationCollection\Core\Factory\FieldDataFactory;
use Netgen\InformationCollection\Core\Mapper\DomainObjectMapper;
use Netgen\InformationCollection\Core\Persistence\Gateway\DoctrineDatabase;
use Netgen\InformationCollection\Doctrine\Repository\EzInfoCollectionAttributeRepository;
use Netgen\InformationCollection\Doctrine\Repository\EzInfoCollectionRepository;

class InformationCollectionService implements InformationCollection
{
    /**
     * @var \Netgen\InformationCollection\Doctrine\Repository\EzInfoCollectionRepository
     */
    protected $ezInfoCollectionRepository;

    /**
     * @var \Netgen\InformationCollection\Doctrine\Repository\EzInfoCollectionAttributeRepository
     */
    protected $ezInfoCollectionAttributeRepository;

    /**
     * @var \eZ\Publish\API\Repository\Repository
     */
    protected $repository;

    /**
     * @var \Netgen\InformationCollection\Core\Persistence\Gateway\DoctrineDatabase
     */
    protected $gateway;

    /**
     * @var \Netgen\InformationCollection\Core\Factory\FieldDataFactory
     */
    protected $fieldsFactory;

    /**
     * @var \Netgen\InformationCollection\Core\Mapper\DomainObjectMapper
     */
    protected $objectMapper;

    /**
     * InformationCollectionService constructor.
     *
     * @param \Netgen\InformationCollection\Doctrine\Repository\EzInfoCollectionRepository $ezInfoCollectionRepository
     * @param \Netgen\InformationCollection\Doctrine\Repository\EzInfoCollectionAttributeRepository $ezInfoCollectionAttributeRepository
     * @param \eZ\Publish\API\Repository\Repository $repository
     * @param \Netgen\InformationCollection\Core\Persistence\Gateway\DoctrineDatabase $gateway
     */
    public function __construct(
        EzInfoCollectionRepository $ezInfoCollectionRepository,
        EzInfoCollectionAttributeRepository $ezInfoCollectionAttributeRepository,
        Repository $repository,
        DoctrineDatabase $gateway,
        FieldDataFactory $factory,
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

    /**
     * {@inheritdoc}
     */
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
        // TODO: Implement filterCollections() method.
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

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function getCollection(CollectionId $collectionId): Collection
    {
        return $this->loadCollection($collectionId->getCollectionId());
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function updateCollectionAttribute(CollectionId $collectionId, Attribute $attribute): void
    {
        $this->ezInfoCollectionAttributeRepository->updateByCollectionId($collectionId, $attribute);
    }

    /**
     * @param int $userId
     * @param mixed $userId
     *
     * @return \eZ\Publish\API\Repository\Values\User\User
     */
    protected function getUser($userId)
    {
        try {
            return $this->repository
                ->getUserService()
                ->loadUser($userId);
        } catch (NotFoundException $exception) {
        }
    }

    /**
     * @param int $collectionId
     *
     * @return \Netgen\InformationCollection\API\Value\Collection
     */
    protected function loadCollection($collectionId)
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
