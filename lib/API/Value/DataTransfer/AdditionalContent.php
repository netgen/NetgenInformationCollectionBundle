<?php

namespace Netgen\InformationCollection\API\Value\DataTransfer;

use Netgen\InformationCollection\API\Value\ValueObject;

class AdditionalContent extends ValueObject
{
    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Content
     */
    public $content;
}
