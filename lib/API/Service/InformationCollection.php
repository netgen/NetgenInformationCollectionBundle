<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Service;

use Netgen\InformationCollection\API\Value\Filter\Query;
use Netgen\InformationCollection\API\Value\ContentsWithCollections;
use Netgen\InformationCollection\API\Value\Collections;
use Netgen\InformationCollection\API\Value\Collection;

interface InformationCollection
{
    /**
     * @param \Netgen\InformationCollection\API\Value\Filter\Query $query
     *
     * @return \Netgen\InformationCollection\API\Value\ContentsWithCollections
     */
    public function getObjectsWithCollections(Query $query): ContentsWithCollections;

    /**
     * Returns collections for given content object
     *
     * @param \Netgen\InformationCollection\API\Value\Filter\Query $query
     *
     * @return \Netgen\InformationCollection\API\Value\Collections
     */
    public function getCollections(Query $query): Collections;

    /**
     * Returns single collection
     *
     * @param \Netgen\InformationCollection\API\Value\Filter\Query $query
     *
     * @return \Netgen\InformationCollection\API\Value\Collection
     */
    public function getCollection(Query $query): Collection;

    /**
     * Returns collection based on search criteria
     *
     * @param \Netgen\InformationCollection\API\Value\Filter\Query $query
     *
     * @return \Netgen\InformationCollection\API\Value\Collections
     */
    public function search(Query $query): Collections;

    /**
     * Removes selected collection fields
     *
     * @param \Netgen\InformationCollection\API\Value\Filter\Query $query
     */
    public function deleteCollectionFields(Query $query): void;

    /**
     * Removes whole collections
     *
     * @param \Netgen\InformationCollection\API\Value\Filter\Query $query
     */
    public function deleteCollections(Query $query): void;

    /**
     * Removes whole collections per content
     *
     * @param \Netgen\InformationCollection\API\Value\Filter\Query $query
     */
    public function deleteCollectionByContent(Query $query): void;
}
