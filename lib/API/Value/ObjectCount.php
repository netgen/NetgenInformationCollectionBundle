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

    public function getCount(): int
    {
        return $this->count;
    }
}
