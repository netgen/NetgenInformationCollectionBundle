<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value\Event;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use Netgen\InformationCollection\API\Value\DataTransfer\AdditionalContent;
use Netgen\InformationCollection\API\Value\InformationCollectionStruct;
use Symfony\Component\EventDispatcher\Event;

final class InformationCollected extends Event
{
    /**
     * @var \Netgen\InformationCollection\API\Value\InformationCollectionStruct
     */
    protected $struct;

    /**
     * @var \Netgen\InformationCollection\API\Value\DataTransfer\AdditionalContent
     */
    protected $additionalContent;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Location
     */
    protected $location;

    /**
     * InformationCollected constructor.
     *
     * @param \Netgen\InformationCollection\API\Value\InformationCollectionStruct $struct
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param \Netgen\InformationCollection\API\Value\DataTransfer\AdditionalContent $additionalContent
     */
    public function __construct(
        InformationCollectionStruct $struct,
        Location $location,
        AdditionalContent $additionalContent
    ) {
        $this->struct = $struct;
        $this->additionalContent = $additionalContent;
        $this->location = $location;
    }

    /**
     * Return collected data.
     *
     * @return \Netgen\InformationCollection\API\Value\InformationCollectionStruct
     */
    public function getInformationCollectionStruct(): InformationCollectionStruct
    {
        return $this->struct;
    }

    /**
     * Return ContentType.
     *
     * @return \eZ\Publish\API\Repository\Values\ContentType\ContentType
     */
    public function getContentType(): ContentType
    {
        return $this->struct
            ->getContentType();
    }

    /**
     * Return Location.
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Content
     */
    public function getContent(): Content
    {
        return $this->struct
            ->getContent();
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\ContentInfo
     */
    public function getContentInfo(): ContentInfo
    {
        return $this->struct
            ->getContent()
            ->contentInfo;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Location
     */
    public function getLocation(): Location
    {
        return $this->location;
    }

    /**
     * Returns additional content
     * This can be ez content or site api content.
     *
     * @return \Netgen\InformationCollection\API\Value\DataTransfer\AdditionalContent
     */
    public function getAdditionalContent(): AdditionalContent
    {
        return $this->additionalContent;
    }
}
