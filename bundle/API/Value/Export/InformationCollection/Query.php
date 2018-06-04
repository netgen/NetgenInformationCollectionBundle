<?php

namespace Netgen\Bundle\InformationCollectionBundle\API\Value\Export\InformationCollection;

use Netgen\Bundle\InformationCollectionBundle\API\Value\ValueObject;

class Query extends ValueObject
{
    const COUNT_QUERY = 0;

    public $contentId;

    public $collectionId;

    public $searchText;

    public $contents;

    public $collections;

    public $fields;

    public $limit = 10;

    public $offset = 0;
}
