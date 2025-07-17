<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Export;

use DateTimeImmutable;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Netgen\InformationCollection\API\Export\ExportResponseFormatter;
use Netgen\InformationCollection\API\Value\Export\Export;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use function array_unshift;
use function file_put_contents;
use function json_encode;
use function str_ends_with;

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

    public function formatToFile(Export $export, Content $content, string $path): File
    {
        $contentName = $content->getName();

        $contents = $export->getContents();

        array_unshift($contents, $export->getHeader());

        $path = str_ends_with($path, '/') ? $path : $path . '/';
        $filePath = $path . $contentName . '-' . (new DateTimeImmutable())->format('YmdHis') . '.json';

        file_put_contents($filePath, json_encode($contents));

        return new File($filePath);
    }
}
