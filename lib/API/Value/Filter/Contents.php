<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value\Filter;

class Contents
{
    protected array $contents;

    public function __construct(array $contents)
    {
        $this->contents = $contents;
    }

    public function getContentIds(): array
    {
        return $this->contents;
    }
}
