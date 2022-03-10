<?php

namespace Netgen\Bundle\InformationCollectionBundle\Event;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use Netgen\Bundle\EzFormsBundle\Form\Payload\InformationCollectionStruct;
use Netgen\Bundle\InformationCollectionBundle\Exception\MissingAdditionalParameterException;
use Symfony\Component\EventDispatcher\Event;
use function array_key_exists;

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
     * @var array
     */
    protected $additionalParameters;

    /**
     * InformationCollected constructor.
     *
     * @param DataWrapper $data
     * @param Content|null $additionalContent
     * @param array $additionalParameters
     */
    public function __construct(DataWrapper $data, Content $additionalContent = null, array $additionalParameters = [])
    {
        $this->data = $data;
        $this->additionalContent = $additionalContent;
        $this->additionalParameters = $additionalParameters;
    }

    /**
     * Return collected data.
     *
     * @return InformationCollectionStruct
     */
    public function getInformationCollectionStruct()
    {
        return $this->data->payload;
    }

    /**
     * Return ContentType.
     *
     * @return ContentType
     */
    public function getContentType()
    {
        return $this->data->definition;
    }

    /**
     * Return Location.
     *
     * @return Location|null
     */
    public function getLocation()
    {
        return $this->data->target;
    }

    /**
     * Returns additional content
     * This can be ez content or site api content.
     *
     * @return Content|null
     */
    public function getAdditionalContent()
    {
        return $this->additionalContent;
    }

    /**
     * @return array
     */
    public function getAdditionalParameters()
    {
        return $this->additionalParameters;
    }

    /**
     * @param array $additionalParameters
     * @return self
     */
    public function setAdditionalParameters(array $additionalParameters)
    {
        $this->additionalParameters = $additionalParameters;
        
        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function setAdditionalParameter($key, $value)
    {
        $this->additionalParameters[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     * @return mixed
     * @throws MissingAdditionalParameterException
     */
    public function getAdditionalParameter($key)
    {
        if (!array_key_exists($key , $this->additionalParameters)) {
            throw new MissingAdditionalParameterException($key);
        }

        return $this->additionalParameters[$key];
    }
}
