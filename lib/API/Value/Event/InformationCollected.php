<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value\Event;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use Netgen\InformationCollection\API\Exception\MissingAdditionalParameterException;
use Netgen\InformationCollection\API\Value\InformationCollectionStruct;
use Symfony\Component\EventDispatcher\Event;
use function array_key_exists;

final class InformationCollected extends Event
{
    /**
     * @var \Netgen\InformationCollection\API\Value\InformationCollectionStruct
     */
    protected $struct;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Location
     */
    protected $location;

    /**
     * @var array
     */
    protected $additionalParameters;

    /**
     * InformationCollected constructor.
     *
     * @param \Netgen\InformationCollection\API\Value\InformationCollectionStruct $struct
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param array $options
     * @param array $additionalParameters
     */
    public function __construct(
        InformationCollectionStruct $struct,
        array $options,
        array $additionalParameters = []
    ) {
        $this->struct = $struct;
        $this->options = $options;
        $this->additionalParameters = $additionalParameters;
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
        return $this->struct
            ->getLocation();
    }

    /**
     * Returns options
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Returns additional parameters
     *
     * @return array
     */
    public function getAdditionalParameters(): array
    {
        return $this->additionalParameters;
    }

    /**
     * Sets additional parameters
     *
     * @param array $additionalParameters
     *
     * @return self
     */
    public function setAdditionalParameters(array $additionalParameters): self
    {
        $this->additionalParameters = $additionalParameters;

        return $this;
    }

    /**
     * Sets additional parameter value
     *
     * @param string $key
     *
     * @param mixed $value
     *
     * @return self
     */
    public function setAdditionalParameter(string $key, $value): self
    {
        $this->additionalParameters[$key] = $value;

        return $this;
    }

    /**
     * Gets additional parameter value
     *
     * @param string $key
     *
     * @return mixed
     *
     * @throws \Netgen\InformationCollection\API\Exception\MissingAdditionalParameterException
     */
    public function getAdditionalParameter(string $key)
    {
        if (!array_key_exists($key , $this->additionalParameters)) {
            throw new MissingAdditionalParameterException($key);
        }

        return $this->additionalParameters[$key];
    }
}
