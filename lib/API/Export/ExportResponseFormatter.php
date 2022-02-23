<?php

namespace Netgen\InformationCollection\API\Export;

use Netgen\InformationCollection\API\Value\Export\Export;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Symfony\Component\HttpFoundation\Response;

interface ExportResponseFormatter
{
    /**
     * @return string
     */
    public function getIdentifier(): string;

    /**
     * @param \Netgen\InformationCollection\API\Value\Export\Export $export
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function format(Export $export, Content $content): Response;
}
