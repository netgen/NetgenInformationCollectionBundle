<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value;

class Collections extends ValueObject
{
    /**
     * @var \Netgen\InformationCollection\API\Value\Collection[]
     */
    protected array $collections;

    protected int $count;

    public function __construct(array $collections, int $count)
    {
        $this->collections = $collections;
        $this->count = $count;
    }

    /**
     * @return \Netgen\InformationCollection\API\Value\Collection[]
     */
    public function getCollections(): array
    {
        return $this->collections;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
