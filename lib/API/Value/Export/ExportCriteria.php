<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value\Export;

use DateTimeInterface;
use Netgen\InformationCollection\API\Value\Filter\ContentId;
use Netgen\InformationCollection\API\Value\Filter\FilterCriteria;

final class ExportCriteria extends FilterCriteria
{
    /**
     * @var string
     */
    protected $exportIdentifier;

    public function __construct(ContentId $contentId, DateTimeInterface $from, DateTimeInterface $to, string $exportIdentifier)
    {
        parent::__construct($contentId, $from, $to);
        $this->exportIdentifier = $exportIdentifier;
    }

    public function getExportIdentifier(): string
    {
        return $this->exportIdentifier;
    }
}
