<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value\Export;

use Netgen\InformationCollection\API\Value\ValueObject;

class Export extends ValueObject
{
    /**
     * @var array
     */
    protected $header;

    /**
     * @var array
     */
    protected $contents;

    public function __construct(array $header, array $contents)
    {
        $this->header = $header;
        $this->contents = $contents;
    }

    /**
     * @return array
     */
    public function getContents(): array
    {
        return $this->contents;
    }

    /**
     * @return array
     */
    public function getHeader(): array
    {
        return $this->header;
    }
}
