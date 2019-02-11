<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value\Export;

use Netgen\InformationCollection\API\Value\ValueObject;

class Export extends ValueObject
{
    /**
     * @var array
     */
    public $header;

    /**
     * @var array
     */
    public $contents;
}
