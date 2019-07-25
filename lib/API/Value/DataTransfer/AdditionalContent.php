<?php

namespace Netgen\InformationCollection\API\Value\DataTransfer;

use Netgen\InformationCollection\API\Value\ValueObject;
use eZ\Publish\API\Repository\Values\Content\Content;

class AdditionalContent extends ValueObject
{
    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Content
     */
    protected $content;

    public function __construct(?Content $content = null)
    {
        $this->content = $content;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Content
     */
    public function getContent(): ?Content
    {
        return $this->content;
    }
}
