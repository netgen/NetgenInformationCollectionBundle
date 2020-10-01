<?php

namespace Netgen\InformationCollection\Core\Export;

use eZ\Publish\API\Repository\Values\Content\Content;
use Netgen\InformationCollection\API\Export\ExportResponseFormatter;
use Netgen\InformationCollection\API\Value\Export\Export;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class JsonExportResponseFormatter implements ExportResponseFormatter
{
    public function getIdentifier(): string
    {
        return 'json_export';
    }

    public function format(Export $export, Content $content): Response
    {
        $contents = $export->getContents();

        array_unshift($contents, $export->getHeader());

        return new JsonResponse($contents);
    }
}
