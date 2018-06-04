<?php

namespace Netgen\Bundle\InformationCollectionBundle\API\Service;

use Netgen\Bundle\InformationCollectionBundle\API\Value\Export\InformationCollection\Query;

interface InformationCollection
{
    /**
     * @param \Netgen\Bundle\InformationCollectionBundle\API\Value\Export\InformationCollection\Query $query
     *
     * @return \Netgen\Bundle\InformationCollectionBundle\API\Value\Export\InformationCollection\ContentsWithCollections
     */
    public function getObjectsWithCollections(Query $query);

    /**
     * @param \Netgen\Bundle\InformationCollectionBundle\API\Value\Export\InformationCollection\Query $query
     *
     * @return \Netgen\Bundle\InformationCollectionBundle\API\Value\Export\InformationCollection\Collections
     */
    public function getCollections(Query $query);

    /**
     * @param \Netgen\Bundle\InformationCollectionBundle\API\Value\Export\InformationCollection\Query $query
     *
     * @return \Netgen\Bundle\InformationCollectionBundle\API\Value\Export\InformationCollection\Collection
     */
    public function getCollection(Query $query);

    /**
     * @param \Netgen\Bundle\InformationCollectionBundle\API\Value\Export\InformationCollection\Query $query
     *
     * @return \Netgen\Bundle\InformationCollectionBundle\API\Value\Export\InformationCollection\Collections
     */
    public function search(Query $query);

    /**
     * @param \Netgen\Bundle\InformationCollectionBundle\API\Value\Export\InformationCollection\Query $query
     */
    public function deleteCollectionFields(Query $query);

    /**
     * @param \Netgen\Bundle\InformationCollectionBundle\API\Value\Export\InformationCollection\Query $query
     */
    public function deleteCollections(Query $query);

    /**
     * @param \Netgen\Bundle\InformationCollectionBundle\API\Value\Export\InformationCollection\Query $query
     */
    public function deleteCollectionByContent(Query $query);
}
