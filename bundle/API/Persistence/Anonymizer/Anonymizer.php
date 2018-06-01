<?php

namespace Netgen\Bundle\InformationCollectionBundle\API\Persistence\Anonymizer;

interface Anonymizer
{
    /**
     * Anonymizes collection with option to anonymize only fields identified by passed ids
     *
     * @param int $collectionId
     * @param array $fields
     */
    public function anonymizeCollection($collectionId, array $fields = []);
}
