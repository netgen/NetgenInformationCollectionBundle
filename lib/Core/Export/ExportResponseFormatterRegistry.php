<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Export;

use Netgen\InformationCollection\API\Export\ExportResponseFormatter;
use RuntimeException;
use function sprintf;

final class ExportResponseFormatterRegistry
{
    /**
     * @var \Netgen\InformationCollection\API\Export\ExportResponseFormatter[]
     */
    private $exportResponseFormatters;

    /**
     * @param \Netgen\InformationCollection\API\Export\ExportResponseFormatter[] $exportResponseFormatters
     */
    public function __construct(iterable $exportResponseFormatters)
    {
        $this->exportResponseFormatters = $exportResponseFormatters;
    }

    /**
     * @param string $identifier
     */
    public function getExportResponseFormatter($identifier): ExportResponseFormatter
    {
        foreach ($this->exportResponseFormatters as $formatter) {
            if ($formatter->getIdentifier() === $identifier) {
                return $formatter;
            }
        }

        throw new RuntimeException(
            sprintf('There are no export formatters with %s identifier available.', $identifier)
        );
    }

    /**
     * @return \Netgen\InformationCollection\API\Export\ExportResponseFormatter[]
     */
    public function getExportResponseFormatters(): iterable
    {
        return $this->exportResponseFormatters;
    }
}
