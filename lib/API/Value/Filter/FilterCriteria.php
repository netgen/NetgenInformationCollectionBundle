<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value\Filter;

use DateTimeInterface;
use Netgen\InformationCollection\API\Value\ValueObject;

class FilterCriteria extends ValueObject
{
    protected DateTimeInterface $from;

    protected DateTimeInterface $to;

    protected ContentId $contentId;

    public function __construct(ContentId $contentId, DateTimeInterface $from, DateTimeInterface $to)
    {
        $this->contentId = $contentId;
        $this->from = $from;
        $this->to = $to;
    }

    public function getFrom(): DateTimeInterface
    {
        return $this->from;
    }

    public function getTo(): DateTimeInterface
    {
        return $this->to;
    }

    public function getContentId(): ContentId
    {
        return $this->contentId;
    }
}
