<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value\Filter;

class Contents
{
    /**
     * @var array
     */
    protected $contents;

    public function __construct(array $contents)
    {
        $this->contents = $contents;
    }

    /**
     * @return array
     */
    public function getContentIds(): array
    {
        return $this->contents;
    }
}
