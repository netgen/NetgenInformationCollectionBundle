<?php

namespace Netgen\Bundle\InformationCollectionBundle\Core\Export;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\Core\Helper\TranslationHelper;
use Netgen\Bundle\InformationCollectionBundle\API\Export\ExportResponseFormatter;
use Netgen\Bundle\InformationCollectionBundle\API\Value\Export\Export;
use Symfony\Component\HttpFoundation\Response;
use PHPExcel;
use PHPExcel_IOFactory;

final class XlsExportResponseFormatter implements ExportResponseFormatter
{
    /**
     * @var \eZ\Publish\Core\Helper\TranslationHelper
     */
    private $translationHelper;

    /**
     * XlsExportResponseFormatter constructor.
     *
     * @param \eZ\Publish\Core\Helper\TranslationHelper $translationHelper
     */
    public function __construct(TranslationHelper $translationHelper)
    {
        $this->translationHelper = $translationHelper;
    }

    public function getIdentifier()
    {
        return 'xls_export';
    }

    public function format(Export $export, Content $content)
    {
        $contentName = $this->translationHelper->getTranslatedContentName($content);

        array_unshift($export->contents, $export->header);
        $excel = new PHPExcel();
        $excel->setActiveSheetIndex(0);

        $activeSheet = $excel->getActiveSheet();

        try {
            $activeSheet->setTitle(substr($contentName, 0, 30));
        } catch (\Exception $exception) {
            $activeSheet->setTitle('Information collection export');
        }

        $activeSheet->fromArray($export->contents);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $contentName . '.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');

        $objWriter->save('php://output');
        unset($objWriter);

        return new Response('');
    }
}
