<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value\DataTransfer;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Netgen\InformationCollection\API\Value\ValueObject;

class AdditionalContent extends ValueObject
{
    protected ?Content $content;

    public function __construct(?Content $content = null)
    {
        $this->content = $content;
    }

    public function getContent(): ?Content
    {
        return $this->content;
    }
}
