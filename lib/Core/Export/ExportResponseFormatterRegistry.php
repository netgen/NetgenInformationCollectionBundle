<?php

namespace Netgen\InformationCollection\Core\Export;

use Netgen\InformationCollection\API\Export\ExportResponseFormatter;
use RuntimeException;

final class ExportResponseFormatterRegistry
{
    /**
     * @var \Netgen\InformationCollection\API\Export\ExportResponseFormatter[]
     */
    protected $exportResponseFormatters;

    /**
     * ExportResponseFormatterRegistry constructor.
     *
     * @param \Netgen\InformationCollection\API\Export\ExportResponseFormatter[] $exportResponseFormatters
     */
    public function __construct(iterable $exportResponseFormatters)
    {
        $this->exportResponseFormatters = $exportResponseFormatters;
    }

    /**
     * @param string $identifier
     *
     * @return \Netgen\InformationCollection\API\Export\ExportResponseFormatter
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
