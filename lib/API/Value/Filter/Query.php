<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value\Filter;

use Netgen\InformationCollection\API\Value\ValueObject;

class Query extends ValueObject
{
    /**
     * Search limit.
     *
     * @var int
     */
    protected $limit = 10;

    /**
     * Search offset.
     *
     * @var int
     */
    protected $offset = 0;

    public function __construct(int $offset, int $limit)
    {
        $this->offset = $offset;
        $this->limit = $limit;
    }

    public static function withLimit(int $limit)
    {
        return new self(0, $limit);
    }

    public static function countQuery()
    {
        return new self(0, 0);
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }
}
