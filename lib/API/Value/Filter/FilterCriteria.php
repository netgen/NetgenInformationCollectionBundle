<?php

namespace Netgen\InformationCollection\API\Value\Filter;

use DateTimeInterface;
use Netgen\InformationCollection\API\Value\ValueObject;

class FilterCriteria extends ValueObject
{
    /**
     * @var \DateTimeInterface
     */
    protected $from;

    /**
     * @var \DateTimeInterface
     */
    protected $to;

    /**
     * @var \Netgen\InformationCollection\API\Value\Filter\ContentId
     */
    protected $contentId;

    public function __construct(ContentId $contentId, DateTimeInterface $from, DateTimeInterface $to)
    {
        $this->contentId = $contentId;
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * @return DateTimeInterface
     */
    public function getFrom(): DateTimeInterface
    {
        return $this->from;
    }

    /**
     * @return DateTimeInterface
     */
    public function getTo(): DateTimeInterface
    {
        return $this->to;
    }

    /**
     * @return ContentId
     */
    public function getContentId(): ContentId
    {
        return $this->contentId;
    }
}
