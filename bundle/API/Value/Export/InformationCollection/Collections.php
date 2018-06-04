<?php

namespace Netgen\Bundle\InformationCollectionBundle\API\Value\Export\InformationCollection;

use Netgen\Bundle\InformationCollectionBundle\API\Value\ValueObject;

class Collections extends ValueObject
{
    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\API\Value\Export\InformationCollection\Collection[]
     */
    public $collections;

    /**
     * @var int
     */
    public $count;
}
