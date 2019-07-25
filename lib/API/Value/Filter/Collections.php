<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value\Filter;

final class Collections
{
    /**
     * @var array
     */
    protected $collections;

    /**
     * @var int
     */
    protected $contentId;

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
