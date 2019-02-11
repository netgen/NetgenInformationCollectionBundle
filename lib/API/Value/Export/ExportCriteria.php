<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value\Export;

use Netgen\InformationCollection\API\Value\ValueObject;

class ExportCriteria extends ValueObject
{
    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Content
     */
    public $content;

    /**
     * @var \DateTime
     */
    public $from;

    /**
     * @var \DateTime
     */
    public $to;
}
