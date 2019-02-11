<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Service;

use Netgen\InformationCollection\API\Value\Export\Export;
use Netgen\InformationCollection\API\Value\Export\ExportCriteria;

interface Exporter
{
    /**
     * Generate Export for give ExportCriteria.
     *
     * @param \Netgen\InformationCollection\API\Value\Export\ExportCriteria $criteria
     *
     * @return \Netgen\InformationCollection\API\Value\Export\Export
     */
    public function export(ExportCriteria $criteria): Export;
}
