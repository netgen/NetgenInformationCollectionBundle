<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value\Event;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\SPI\Persistence\Content\ContentInfo;
use Netgen\InformationCollection\API\Value\DataTransfer\AdditionalContent;
use Netgen\InformationCollection\Integration\RepositoryForms\InformationCollectionData;
use Symfony\Component\EventDispatcher\Event;

final class InformationCollected extends Event
{
    /**
     * @var \Netgen\InformationCollection\Integration\RepositoryForms\InformationCollectionData
     */
    protected $data;

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
     * @param \Netgen\InformationCollection\Integration\RepositoryForms\InformationCollectionData $data
     * @param \eZ\Publish\API\Repository\Values\Content\Content $additionalContent
     */
    public function __construct(
        InformationCollectionData $data,
        Location $location,
        AdditionalContent $additionalContent
    )
    {
        $this->data = $data;
        $this->additionalContent = $additionalContent;
        $this->location = $location;
    }

    /**
     * Return collected data.
     *
     * @return \Netgen\InformationCollection\Integration\RepositoryForms\InformationCollectionData
     */
    public function getInformationCollectionStruct(): InformationCollectionData
    {
        return $this->data;
    }

    /**
     * Return ContentType.
     *
     * @return \eZ\Publish\API\Repository\Values\ContentType\ContentType
     */
    public function getContentType(): ContentType
    {
        return $this->data
            ->contentDraft
            ->getContentType();
    }

    /**
     * Return Location.
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Content
     */
    public function getContent(): Content
    {
        return $this->data
            ->contentDraft;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\ContentInfo
     */
    public function getContentInfo(): ContentInfo
    {
        return $this->data
            ->contentDraft
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
