<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value;

use Ibexa\Contracts\Core\Repository\Values\Content\Content as APIContent;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;

final class Content extends ValueObject
{
    /**
     * @var bool
     */
    protected $hasLocation;

    /**
     * @var APIContent
     */
    protected $content;

    /**
     * @var ContentType
     */
    protected $contentType;

    /**
     * @var Collection
     */
    protected $firstCollection;

    /**
     * @var Collection
     */
    protected $lastCollection;

    /**
     * @var int
     */
    protected $childCount;

    public function __construct(
        APIContent $content,
        ContentType $contentType,
        Collection $firstCollection,
        Collection $lastCollection,
        int $childCount,
        bool $hasLocation
    ) {
        $this->hasLocation = $hasLocation;
        $this->content = $content;
        $this->contentType = $contentType;
        $this->firstCollection = $firstCollection;
        $this->lastCollection = $lastCollection;
        $this->childCount = $childCount;
    }

    /**
     * @return APIContent
     */
    public function getContent(): APIContent
    {
        return $this->content;
    }

    /**
     * @return ContentType
     */
    public function getContentType(): ContentType
    {
        return $this->contentType;
    }

    /**
     * @return Collection
     */
    public function getFirstCollection(): Collection
    {
        return $this->firstCollection;
    }

    /**
     * @return Collection
     */
    public function getLastCollection(): Collection
    {
        return $this->lastCollection;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->childCount;
    }

    /**
     * @return bool
     */
    public function hasLocation(): bool
    {
        return $this->hasLocation;
    }
}
