<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value\Export;

use DateTimeInterface;
use eZ\Publish\API\Repository\Values\Content\Content;
use Netgen\InformationCollection\API\Value\ValueObject;

class ExportCriteria extends ValueObject
{
    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Content
     */
    protected $content;

    /**
     * @var \DateTimeInterface
     */
    protected $from;

    /**
     * @var \DateTimeInterface
     */
    protected $to;

    /**
     * @var int
     */
    protected $offset;

    /**
     * @var int
     */
    protected $limit;

    public function __construct(Content $content, DateTimeInterface $from, DateTimeInterface $to, int $offset = 0, int $limit = 100)
    {
        $this->content = $content;
        $this->from = $from;
        $this->to = $to;
        $this->offset = $offset;
        $this->limit = $limit;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Content
     */
    public function getContent(): Content
    {
        return $this->content;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getFrom(): DateTimeInterface
    {
        return $this->from;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getTo(): DateTimeInterface
    {
        return $this->to;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }
}
