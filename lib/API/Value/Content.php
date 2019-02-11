<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value;

class Content extends ValueObject
{
    /**
     * @var \Netgen\InformationCollection\API\Value\Content
     */
    public $content;

    /**
     * @var \Netgen\InformationCollection\Doctrine\Entity\EzInfoCollection
     */
    public $firstCollection;

    /**
     * @var \Netgen\InformationCollection\Doctrine\Entity\EzInfoCollection
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
