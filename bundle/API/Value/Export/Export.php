<?php

namespace Netgen\Bundle\InformationCollectionBundle\API\Value\Export;

use Netgen\Bundle\InformationCollectionBundle\API\Value\ValueObject;

class Export extends ValueObject
{
    /**
     * @var array
     */
    public $header;

    /**
     * @var array
     */
    public $contents;
}
