<?php

namespace Netgen\Bundle\InformationCollectionBundle\Event;

use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\Content\Location;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use Netgen\Bundle\EzFormsBundle\Form\Payload\InformationCollectionStruct;
use Netgen\EzPlatformSiteApi\API\Values\Content;
use Symfony\Component\EventDispatcher\Event;

class InformationCollected extends Event
{
    /**
     * @var DataWrapper
     */
    protected $data;

    /**
     * @var Content|null
     */
    protected $additionalContent;

    /**
     * InformationCollected constructor.
     *
     * @param DataWrapper $data
     * @param Content $additionalContent
     */
    public function __construct(DataWrapper $data, $additionalContent = null)
    {
        $this->data = $data;
        $this->additionalContent = $additionalContent;
    }

    /**
     * Return collected data
     *
     * @return InformationCollectionStruct
     */
    public function getInformationCollectionStruct()
    {
         return $this->data->payload;
    }

    /**
     * Return ContentType
     *
     * @return ContentType
     */
    public function getContentType()
    {
        return $this->data->definition;
    }

    /**
     * Return Location
     *
     * @return Location|null
     */
    public function getLocation()
    {
        return $this->data->target;
    }

    /**
     * Returns additional content
     * This can be ez content or site api content
     */
    public function getAdditionalContent()
    {
        return $this->additionalContent;
    }
}
