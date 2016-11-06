<?php

namespace Netgen\Bundle\InformationCollectionBundle\Event;

use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\Content\Location;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use Netgen\Bundle\EzFormsBundle\Form\Payload\InformationCollectionStruct;
use Symfony\Component\EventDispatcher\Event;

class InformationCollected extends Event
{
    /**
     * @var DataWrapper
     */
    protected $data;

    /**
     * InformationCollected constructor.
     *
     * @param DataWrapper $data
     */
    public function __construct(DataWrapper $data)
    {
        $this->data = $data;
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
}