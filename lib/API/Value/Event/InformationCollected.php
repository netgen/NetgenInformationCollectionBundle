<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value\Event;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Netgen\InformationCollection\API\Exception\MissingAdditionalParameterException;
use Netgen\InformationCollection\API\Value\InformationCollectionStruct;
use Symfony\Contracts\EventDispatcher\Event;
use function array_key_exists;

final class InformationCollected extends Event
{
    protected InformationCollectionStruct $struct;

    protected array $options;

    protected array $additionalParameters;

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
     */
    public function getInformationCollectionStruct(): InformationCollectionStruct
    {
        return $this->struct;
    }

    /**
     * Return ContentType.
     */
    public function getContentType(): ContentType
    {
        return $this->struct
            ->getContentType();
    }

    /**
     * Return Location.
     */
    public function getContent(): Content
    {
        return $this->struct
            ->getContent();
    }

    public function getContentInfo(): ContentInfo
    {
        return $this->struct
            ->getContent()
            ->contentInfo;
    }

    public function getLocation(): Location
    {
        return $this->struct
            ->getLocation();
    }

    /**
     * Returns options.
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Returns additional parameters.
     */
    public function getAdditionalParameters(): array
    {
        return $this->additionalParameters;
    }

    /**
     * Sets additional parameters.
     */
    public function setAdditionalParameters(array $additionalParameters): self
    {
        $this->additionalParameters = $additionalParameters;

        return $this;
    }

    /**
     * Sets additional parameter value.
     *
     * @param mixed $value
     */
    public function setAdditionalParameter(string $key, $value): self
    {
        $this->additionalParameters[$key] = $value;

        return $this;
    }

    /**
     * Gets additional parameter value.
     *
     * @throws \Netgen\InformationCollection\API\Exception\MissingAdditionalParameterException
     *
     * @return mixed
     */
    public function getAdditionalParameter(string $key)
    {
        if (!array_key_exists($key, $this->additionalParameters)) {
            throw new MissingAdditionalParameterException($key);
        }

        return $this->additionalParameters[$key];
    }
}
