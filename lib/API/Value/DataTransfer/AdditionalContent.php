<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value\DataTransfer;

use eZ\Publish\API\Repository\Values\Content\Content;
use Netgen\InformationCollection\API\Value\ValueObject;

class AdditionalContent extends ValueObject
{
    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Content|null
     */
    protected $content;

    /**
     * AdditionalContent constructor.
     *
     * @param Content|null $content
     */
    public function __construct(?Content $content = null)
    {
        $this->content = $content;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Content|null
     */
    public function getContent(): ?Content
    {
        return $this->content;
    }
}
