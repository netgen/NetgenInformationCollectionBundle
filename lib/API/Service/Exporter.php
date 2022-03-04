<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Service;

use Netgen\InformationCollection\API\Value\Export\Export;
use Netgen\InformationCollection\API\Value\Export\ExportCriteria;

interface Exporter
{
    /**
     * Generate Export for given ExportCriteria.
     */
    public function export(ExportCriteria $criteria): Export;
}
