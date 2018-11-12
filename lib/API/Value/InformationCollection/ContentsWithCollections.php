<?php

namespace Netgen\Bundle\InformationCollectionBundle\API\Value\InformationCollection;

use Netgen\Bundle\InformationCollectionBundle\API\Value\ValueObject;

class ContentsWithCollections extends ValueObject
{
    /**
     * @var int
     */
    public $count;

    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\API\Value\InformationCollection\Content
     */
    public $contents;
}
