<?php

namespace Netgen\Bundle\InformationCollectionBundle\Core\Export;

use RuntimeException;

final class ExportResponseFormatterRegistry
{
    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\API\Export\ExportResponseFormatter[]
     */
    protected $exportResponseFormatters;

    /**
     * ExportResponseFormatterRegistry constructor.
     *
     * @param \Netgen\Bundle\InformationCollectionBundle\API\Export\ExportResponseFormatter[] $exportResponseFormatters
     */
    public function __construct(array $exportResponseFormatters = [])
    {
        $this->exportResponseFormatters = $exportResponseFormatters;
    }

    /**
     * @param string $identifier
     *
     * @return \Netgen\Bundle\InformationCollectionBundle\API\Export\ExportResponseFormatter
     */
    public function getExportResponseFormatter($identifier)
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
     * @return \Netgen\Bundle\InformationCollectionBundle\API\Export\ExportResponseFormatter[]
     */
    public function getExportResponseFormatters()
    {
        return $this->exportResponseFormatters;
    }
}
