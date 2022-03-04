<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value\Event;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Netgen\InformationCollection\API\Value\InformationCollectionStruct;
use Symfony\Contracts\EventDispatcher\Event;

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
     * @var \Ibexa\Contracts\Core\Repository\Values\Content\Location
     */
    protected $location;

    public function __construct(
        InformationCollectionStruct $struct,
        array $options
    ) {
        $this->struct = $struct;
        $this->options = $options;
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
}
