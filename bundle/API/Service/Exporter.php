<?php

namespace Netgen\Bundle\InformationCollectionBundle\API\Service;

use Netgen\Bundle\InformationCollectionBundle\API\Value\Export\ExportCriteria;

interface Exporter
{
    /**
     * @param int $contentId
     *
     * @return \Netgen\Bundle\InformationCollectionBundle\API\Value\Export\Export
     */
    public function export(ExportCriteria $criteria);
}
