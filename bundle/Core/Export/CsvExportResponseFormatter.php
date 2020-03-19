<?php

namespace Netgen\Bundle\InformationCollectionBundle\Core\Export;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\Core\Helper\TranslationHelper;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use League\Csv\Writer;
use SplTempFileObject;
use Netgen\Bundle\InformationCollectionBundle\API\Export\ExportResponseFormatter;
use Netgen\Bundle\InformationCollectionBundle\API\Value\Export\Export;
use Symfony\Component\HttpFoundation\Response;

final class CsvExportResponseFormatter implements ExportResponseFormatter
{
    /**
     * @var \eZ\Publish\Core\Helper\TranslationHelper
     */
    private $translationHelper;

    /**
     * @var \eZ\Publish\Core\MVC\ConfigResolverInterface
     */
    private $configResolver;

    /**
     * CsvExportResponseFormatter constructor.
     *
     * @param \eZ\Publish\Core\Helper\TranslationHelper $translationHelper
     * @param \eZ\Publish\Core\MVC\ConfigResolverInterface $configResolver
     */
    public function __construct(TranslationHelper $translationHelper, ConfigResolverInterface $configResolver)
    {
        $this->translationHelper = $translationHelper;
        $this->configResolver = $configResolver;
    }

    public function getIdentifier()
    {
        return 'csv_export';
    }

    public function format(Export $export, Content $content)
    {
        $contentName = $this->translationHelper->getTranslatedContentName($content);

        $config = $this->configResolver->getParameter('csv_export', 'netgen_information_collection');

        $writer = Writer::createFromFileObject(new SplTempFileObject());
        $writer->setDelimiter($config['delimiter']);
        $writer->setEnclosure($config['enclosure']);
        $writer->setNewline($config['newline']);
        $writer->setOutputBOM(Writer::BOM_UTF8); //adding the BOM sequence on output
        $writer->insertOne($export->header);
        $writer->insertAll($export->contents);

        $writer->output($contentName . '.csv');
        return new Response('');
    }
}
