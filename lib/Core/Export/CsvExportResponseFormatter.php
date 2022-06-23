<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Export;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\Helper\TranslationHelper;
use League\Csv\Writer;
use Netgen\InformationCollection\API\Export\ExportResponseFormatter;
use Netgen\InformationCollection\API\Value\Export\Export;
use SplTempFileObject;
use Symfony\Component\HttpFoundation\Response;

final class CsvExportResponseFormatter implements ExportResponseFormatter
{
    private TranslationHelper $translationHelper;

    private ConfigResolverInterface $configResolver;

    public function __construct(TranslationHelper $translationHelper, ConfigResolverInterface $configResolver)
    {
        $this->translationHelper = $translationHelper;
        $this->configResolver = $configResolver;
    }

    public function getIdentifier(): string
    {
        return 'csv_export';
    }

    public function format(Export $export, Content $content): Response
    {
        $contentName = $this->translationHelper->getTranslatedContentName($content);

        $config = $this->configResolver->getParameter('export', 'netgen_information_collection');
        $config = $config['csv'];

        $writer = Writer::createFromFileObject(new SplTempFileObject());
        $writer->setDelimiter($config['delimiter']);
        $writer->setEnclosure($config['enclosure']);
        $writer->setNewline($config['newline']);
        $writer->setOutputBOM(Writer::BOM_UTF8); // adding the BOM sequence on output
        $writer->insertOne($export->getHeader());
        $writer->insertAll($export->getContents());

        $writer->output($contentName . '.csv');

        return new Response('');
    }
}
