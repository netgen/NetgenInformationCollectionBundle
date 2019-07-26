<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Service;

use Netgen\InformationCollection\API\Value\Collection;
use Netgen\InformationCollection\API\Value\CollectionCount;
use Netgen\InformationCollection\API\Value\Collections;
use Netgen\InformationCollection\API\Value\ContentsWithCollections;
use Netgen\InformationCollection\API\Value\Filter\CollectionFields;
use Netgen\InformationCollection\API\Value\Filter\CollectionId;
use Netgen\InformationCollection\API\Value\Filter\Collections as FilterCollections;
use Netgen\InformationCollection\API\Value\Filter\ContentId;
use Netgen\InformationCollection\API\Value\Filter\Contents;
use Netgen\InformationCollection\API\Value\Filter\Query;
use Netgen\InformationCollection\API\Value\Filter\SearchCountQuery;
use Netgen\InformationCollection\API\Value\Filter\SearchQuery;
use Netgen\InformationCollection\API\Value\InformationCollectionStruct;
use Netgen\InformationCollection\API\Value\ObjectCount;
use Netgen\InformationCollection\API\Value\SearchCount;

interface InformationCollection
{
    /**
     * @param \Netgen\InformationCollection\API\Value\InformationCollectionStruct $struct
     *
     * @throws \Netgen\InformationCollection\API\Exception\PersistingFailedException
     */
    public function createCollection(InformationCollectionStruct $struct): void;

    /**
     * @return \Netgen\InformationCollection\API\Value\ObjectCount
     */
    public function getObjectsWithCollectionsCount(): ObjectCount;

    /**
     * @param \Netgen\InformationCollection\API\Value\Filter\Query $query
     *
     * @return \Netgen\InformationCollection\API\Value\ContentsWithCollections
     */
    public function getObjectsWithCollections(Query $query): ContentsWithCollections;

    /**
     * @param \Netgen\InformationCollection\API\Value\Filter\ContentId $contentId
     *
     * @return \Netgen\InformationCollection\API\Value\CollectionCount
     */
    public function getCollectionsCount(ContentId $contentId): CollectionCount;

    /**
     * Returns collections for given content object.
     *
     * @param \Netgen\InformationCollection\API\Value\Filter\ContentId $contentId
     *
     * @return \Netgen\InformationCollection\API\Value\Collections
     */
    public function getCollections(ContentId $contentId): Collections;

    /**
     * Returns single collection.
     *
     * @param \Netgen\InformationCollection\API\Value\Filter\CollectionId $collectionId
     *
     * @return \Netgen\InformationCollection\API\Value\Collection
     */
    public function getCollection(CollectionId $collectionId): Collection;

    /**
     * Returns collection based on search criteria.
     *
     * @param \Netgen\InformationCollection\API\Value\Filter\SearchQuery $query
     *
     * @return \Netgen\InformationCollection\API\Value\Collections
     */
    public function search(SearchQuery $query): Collections;

    /**
     * Returns collection count based on search criteria.
     *
     * @param \Netgen\InformationCollection\API\Value\Filter\SearchCountQuery $query
     *
     * @return \Netgen\InformationCollection\API\Value\Collections
     */
    public function searchCount(SearchCountQuery $query): SearchCount;

    /**
     * Removes selected collection fields.
     *
     * @param \Netgen\InformationCollection\API\Value\Filter\CollectionFields
     */
    public function deleteCollectionFields(CollectionFields $collectionFields): void;

    /**
     * Removes whole collections.
     *
     * @param \Netgen\InformationCollection\API\Value\Filter\Collections $collections
     */
    public function deleteCollections(FilterCollections $collections): void;

    /**
     * Removes whole collections per content.
     *
     * @param \Netgen\InformationCollection\API\Value\Filter\Contents $contents
     */
    public function deleteCollectionByContent(Contents $contents): void;
}
