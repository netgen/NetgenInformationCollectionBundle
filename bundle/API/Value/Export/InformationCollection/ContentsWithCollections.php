<?php

namespace Netgen\Bundle\InformationCollectionBundle\API\Value\Export\InformationCollection;

use Netgen\Bundle\InformationCollectionBundle\API\Value\ValueObject;

class ContentsWithCollections extends ValueObject
{
    /**
     * @var int
     */
    public $count;

    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\API\Value\Export\InformationCollection\Content
     */
    public $contents;
}
