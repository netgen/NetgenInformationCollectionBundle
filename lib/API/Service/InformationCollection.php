<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Service;

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

interface InformationCollection
{
    /**
     * @throws \Netgen\InformationCollection\API\Exception\PersistingFailedException
     */
    public function createCollection(InformationCollectionStruct $struct): void;

    public function getObjectsWithCollectionsCount(): ObjectCount;

    public function getObjectsWithCollections(Query $query): ContentsWithCollections;

    public function getCollectionsCount(ContentId $contentId): CollectionCount;

    /**
     * Returns collections for given content object.
     */
    public function getCollections(ContentId $contentId): Collections;

    /**
     * Returns collections for given content object.
     */
    public function filterCollections(FilterCriteria $criteria): Collections;

    /**
     * Returns single collection.
     */
    public function getCollection(CollectionId $collectionId): Collection;

    /**
     * Returns collection based on search criteria.
     */
    public function search(SearchQuery $query): Collections;

    /**
     * Returns collection count based on search criteria.
     */
    public function searchCount(SearchCountQuery $query): SearchCount;

    /**
     * Removes selected collection fields.
     */
    public function deleteCollectionFields(CollectionFields $collectionFields): void;

    /**
     * Removes whole collections.
     */
    public function deleteCollections(FilterCollections $collections): void;

    /**
     * Removes whole collections per content.
     */
    public function deleteCollectionByContent(Contents $contents): void;

    /**
     * Updates Attribute value for given Attribute.
     */
    public function updateCollectionAttribute(CollectionId $collectionId, Attribute $attribute): void;
}
