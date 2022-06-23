<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value\Filter;

class SearchQuery extends Query
{
    protected int $contentId;

    protected string $searchText;

    public function __construct(int $contentId, string $searchText, int $offset, int $limit)
    {
        $this->contentId = $contentId;
        $this->searchText = $searchText;
        parent::__construct($offset, $limit);
    }

    public static function withContentAndSearchText(int $contentId, string $searchText): self
    {
        return new self($contentId, $searchText, 0, 0);
    }

    public function getContentId(): int
    {
        return $this->contentId;
    }

    public function getSearchText(): string
    {
        return $this->searchText;
    }
}
