<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value\Filter;

class SearchQuery extends Query
{
    /**
     * @var int
     */
    protected $contentId;

    /**
     * @var string
     */
    protected $searchText;

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

    /**
     * @return int
     */
    public function getContentId(): int
    {
        return $this->contentId;
    }

    /**
     * @return string
     */
    public function getSearchText(): string
    {
        return $this->searchText;
    }
}
