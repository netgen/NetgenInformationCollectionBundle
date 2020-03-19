<?php

namespace Netgen\Bundle\InformationCollectionBundle\API\Export;

use Netgen\Bundle\InformationCollectionBundle\API\Value\Export\Export;
use eZ\Publish\API\Repository\Values\Content\Content;

interface ExportResponseFormatter
{
    /**
     * @return string
     */
    public function getIdentifier();

    /**
     * @param \Netgen\Bundle\InformationCollectionBundle\API\Value\Export\Export $export
     * @param \eZ\Publish\API\Repository\Values\Content\Content $content
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function format(Export $export, Content $content);
}
