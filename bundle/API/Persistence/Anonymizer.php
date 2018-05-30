<?php

namespace Netgen\Bundle\InformationCollectionBundle\API\Persistence;

interface Anonymizer
{
    /**
     * Anonymizes collections that are instances of give content form
     *
     * @param int $contentId
     */
    public function anonymizeByContent($contentId);

    /**
     * Anonymizes collection fields identified by passed ids
     * that are instances of given content form
     *
     * @param int $contentId
     * @param array $fields
     */
    public function anonymizeByContentAndFields($contentId, array $fields);

    /**
     * Anonymizes single collection identified by id
     *
     * @param int $collectionId
     */
    public function anonymizeByCollection($collectionId);

    /**
     * Anonymizes collection fields identified by passed ids
     * for collection identified by id
     *
     * @param int $collectionId
     * @param array $fields
     */
    public function anonymizeByCollectionAndFields($collectionId, array $fields);
}
