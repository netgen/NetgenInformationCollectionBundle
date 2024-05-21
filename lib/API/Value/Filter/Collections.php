<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value\Filter;

final class Collections
{
    /**
     * @var array<int, int>
     *
     * Array of ids of collections.
     */
    private $collections;

    /**
     * @var int
     */
    private $contentId;

    public function __construct(int $contentId, array $collections)
    {
        $this->collections = $collections;
        $this->contentId = $contentId;
    }

    public function getCollectionIds(): array
    {
        return $this->collections;
    }

    /**
     * @return int
     */
    public function getContentId(): int
    {
        return $this->contentId;
    }
}
