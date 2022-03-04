<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value\Filter;

use Netgen\InformationCollection\API\Value\ValueObject;

class Query extends ValueObject
{
    protected int $limit;

    protected int $offset;

    public function __construct(int $offset, int $limit)
    {
        $this->offset = $offset;
        $this->limit = $limit;
    }

    public static function withLimit(int $limit): self
    {
        return new self(0, $limit);
    }

    public static function countQuery(): self
    {
        return new self(0, 0);
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }
}
