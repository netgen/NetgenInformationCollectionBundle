<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value\Export;

use Netgen\InformationCollection\API\Value\ValueObject;

class Export extends ValueObject
{
    protected array $header;

    protected array $contents;

    public function __construct(array $header, array $contents)
    {
        $this->header = $header;
        $this->contents = $contents;
    }

    public function getContents(): array
    {
        return $this->contents;
    }

    public function getHeader(): array
    {
        return $this->header;
    }
}
