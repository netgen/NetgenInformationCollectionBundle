<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value\Filter;

final class ContentId extends Query
{
    protected int $id;

    public function __construct(int $id, int $offset, int $limit)
    {
        $this->id = $id;
        parent::__construct($offset, $limit);
    }

    public function getContentId(): int
    {
        return $this->id;
    }

    public static function withContentId(int $contentId): self
    {
        return new self($contentId, 0, 0);
    }

    public static function countWithContentId(int $id): self
    {
        return new self($id, 0, 0);
    }
}
