<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Export;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Netgen\InformationCollection\API\Value\Export\Export;
use Symfony\Component\HttpFoundation\Response;

interface ExportResponseFormatter
{
    public function getIdentifier(): string;

    public function format(Export $export, Content $content): Response;
}
