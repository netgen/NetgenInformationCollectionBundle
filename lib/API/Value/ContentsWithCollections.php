<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value;

class ContentsWithCollections extends ValueObject
{
    /**
     * @var int
     */
    public $count;

    /**
     * @var \Netgen\InformationCollection\API\Value\Content
     */
    public $contents;
}
