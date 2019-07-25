<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value\Export;

use eZ\Publish\API\Repository\Values\Content\Content;
use Netgen\InformationCollection\API\Value\ValueObject;
use DateTimeInterface;

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

    public function __construct(Content $content, DateTimeInterface $from, DateTimeInterface $to)
    {
        $this->content = $content;
        $this->from = $from;
        $this->to = $to;
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
}
