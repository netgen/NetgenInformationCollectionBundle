<?php

namespace Netgen\Bundle\InformationCollectionBundle\API\Value\Export;

use Netgen\Bundle\InformationCollectionBundle\API\Value\ValueObject;

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
