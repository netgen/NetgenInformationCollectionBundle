<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value;

class Collections extends ValueObject
{
    /**
     * @var \Netgen\InformationCollection\API\Value\Collection[]
     */
    public $collections;

    /**
     * @var int
     */
    public $count;
}
