<?php

namespace Netgen\Bundle\InformationCollectionBundle\API\Value\InformationCollection;

use Netgen\Bundle\InformationCollectionBundle\API\Value\ValueObject;

class Query extends ValueObject
{
    /**
     * Get only count without actual data
     */
    const COUNT_QUERY = 0;

    /**
     * Single content id
     *
     * @var int
     */
    public $contentId;

    /**
     * Single collection id
     *
     * @var int
     */
    public $collectionId;

    /**
     * String text to search for
     *
     * @var string
     */
    public $searchText;

    /**
     * Array of content ids
     *
     * @var array
     */
    public $contents;

    /**
     * Array of collection ids
     *
     * @var array
     */
    public $collections;

    /**
     * Array of fields ids
     *
     * @var array
     */
    public $fields;

    /**
     * Search limit
     *
     * @var int
     */
    public $limit = 10;

    /**
     * Search offset
     *
     * @var int
     */
    public $offset = 0;

    /**
     * Creates query with content id as argument
     *
     * @param int $contentId
     *
     * @return self
     */
    public static function withContent($contentId)
    {
        return new self([
            'contentId' => $contentId,
            'offset' => 0,
        ]);
    }

    /**
     * Creates count query
     *
     * @return self
     */
    public static function count()
    {
        return new self([
            'limit' => 0,
            'offset' => 0,
        ]);
    }
}
