<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value;

use Ibexa\Contracts\Core\Repository\Values\Content\Content as APIContent;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;

final class Content extends ValueObject
{
    protected bool $hasLocation;

    protected APIContent $content;

    protected ContentType $contentType;

    protected Collection $firstCollection;

    protected Collection $lastCollection;

    protected int $childCount;

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

    public function getContent(): APIContent
    {
        return $this->content;
    }

    public function getContentType(): ContentType
    {
        return $this->contentType;
    }

    public function getFirstCollection(): Collection
    {
        return $this->firstCollection;
    }

    public function getLastCollection(): Collection
    {
        return $this->lastCollection;
    }

    public function getCount(): int
    {
        return $this->childCount;
    }

    public function hasLocation(): bool
    {
        return $this->hasLocation;
    }
}
