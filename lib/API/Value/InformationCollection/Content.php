<?php

namespace Netgen\Bundle\InformationCollectionBundle\API\Value\InformationCollection;

use Netgen\Bundle\InformationCollectionBundle\API\Value\ValueObject;

class Content extends ValueObject
{
    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\API\Value\InformationCollection\Content
     */
    public $content;

    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\Entity\EzInfoCollection
     */
    public $firstCollection;

    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\Entity\EzInfoCollection
     */
    public $lastCollection;

    /**
     * @var \eZ\Publish\API\Repository\Values\ContentType\ContentType
     */
    public $contentType;

    /**
     * @var int
     */
    public $count;

    /**
     * @var bool
     */
    public $hasLocation;
}
