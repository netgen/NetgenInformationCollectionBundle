<?php

namespace Netgen\InformationCollection\Core\Export;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Core\Helper\TranslationHelper;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use League\Csv\Writer;
use SplTempFileObject;
use Netgen\InformationCollection\API\Export\ExportResponseFormatter;
use Netgen\InformationCollection\API\Value\Export\Export;
use Symfony\Component\HttpFoundation\Response;

final class CsvExportResponseFormatter implements ExportResponseFormatter
{
    /**
     * @var \Ibexa\Core\Helper\TranslationHelper
     */
    private $translationHelper;

    /**
     * @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface
     */
    private $configResolver;

    /**
     * CsvExportResponseFormatter constructor.
     *
     * @param \Ibexa\Core\Helper\TranslationHelper $translationHelper
     * @param \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface $configResolver
     */
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
        $writer->setOutputBOM(Writer::BOM_UTF8); //adding the BOM sequence on output
        $writer->insertOne($export->getHeader());
        $writer->insertAll($export->getContents());

        $writer->output($contentName . '.csv');
        return new Response('');
    }
}
