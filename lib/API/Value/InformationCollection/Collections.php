<?php

namespace Netgen\Bundle\InformationCollectionBundle\API\Value\InformationCollection;

use Netgen\Bundle\InformationCollectionBundle\API\Value\ValueObject;

class Collections extends ValueObject
{
    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\API\Value\InformationCollection\Collection[]
     */
    public $collections;

    /**
     * @var int
     */
    public $count;
}
