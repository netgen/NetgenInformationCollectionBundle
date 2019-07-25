<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value;

class ObjectCount extends ValueObject
{
    protected $count;

    public function __construct(int $count)
    {
        $this->count = $count;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }
}
