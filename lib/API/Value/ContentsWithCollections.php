<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value;

class ContentsWithCollections extends ValueObject
{
    /**
     * @var \Netgen\InformationCollection\API\Value\Content[]
     */
    protected array $contents;

    protected int $count;

    public function __construct(array $contents, int $count)
    {
        $this->count = $count;
        $this->contents = $contents;
    }

    /**
     * @return \Netgen\InformationCollection\API\Value\Content[]
     */
    public function getContents(): array
    {
        return $this->contents;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
