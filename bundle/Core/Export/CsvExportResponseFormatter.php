<?php

namespace Netgen\Bundle\InformationCollectionBundle\Core\Export;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\Core\Helper\TranslationHelper;
use League\Csv\Writer;
use SplTempFileObject;
use Netgen\Bundle\InformationCollectionBundle\API\Export\ExportResponseFormatter;
use Netgen\Bundle\InformationCollectionBundle\API\Value\Export\Export;
use Symfony\Component\HttpFoundation\Response;

final class CsvExportResponseFormatter implements ExportResponseFormatter
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var \eZ\Publish\Core\Helper\TranslationHelper
     */
    private $translationHelper;

    public function __construct(TranslationHelper $translationHelper, array $config)
    {
        $this->config = $config;
        $this->translationHelper = $translationHelper;
    }

    public function getIdentifier()
    {
        return 'csv_export';
    }

    public function format(Export $export, Content $content)
    {
        $contentName = $this->translationHelper->getTranslatedContentName($content);

        $writer = Writer::createFromFileObject(new SplTempFileObject());
        $writer->setDelimiter($this->config['delimiter']);
        $writer->setEnclosure($this->config['enclosure']);
        $writer->setNewline($this->config['newline']);
        $writer->setOutputBOM(Writer::BOM_UTF8); //adding the BOM sequence on output
        $writer->insertOne($export->header);
        $writer->insertAll($export->contents);

        $writer->output($contentName . '.csv');
        return new Response('');
    }
}
