<?php

namespace Netgen\Bundle\InformationCollectionBundle\Event;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use Netgen\Bundle\EzFormsBundle\Form\Payload\InformationCollectionStruct;

interface InformationCollectedInterface
{
    /**
     * Return collected data.
     *
     * @return InformationCollectionStruct
     */
    public function getInformationCollectionStruct();

    /**
     * Return ContentType.
     *
     * @return ContentType
     */
    public function getContentType();

    /**
     * Return Location.
     *
     * @return Location|null
     */
    public function getLocation();

    /**
     * Returns additional content
     * This can be ez content or site api content.
     *
     * @return Content|null
     */
    public function getAdditionalContent();
}
