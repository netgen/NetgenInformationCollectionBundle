<?php

namespace Netgen\InformationCollection\Core\Export;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Core\Helper\TranslationHelper;
use Netgen\InformationCollection\API\Export\ExportResponseFormatter;
use Netgen\InformationCollection\API\Value\Export\Export;
use Symfony\Component\HttpFoundation\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

final class XlsExportResponseFormatter implements ExportResponseFormatter
{
    /**
     * @var \Ibexa\Core\Helper\TranslationHelper
     */
    private $translationHelper;

    /**
     * XlsExportResponseFormatter constructor.
     *
     * @param \Ibexa\Core\Helper\TranslationHelper $translationHelper
     */
    public function __construct(TranslationHelper $translationHelper)
    {
        $this->translationHelper = $translationHelper;
    }

    public function getIdentifier(): string
    {
        return 'phpexcel_xls_export';
    }

    public function format(Export $export, Content $content): Response
    {
        $contentName = $this->translationHelper->getTranslatedContentName($content);

        $spreadsheet = new Spreadsheet();
        $activeSheet = $spreadsheet->getActiveSheet();

        try {
            $activeSheet->setTitle(substr($contentName, 0, 30));
        } catch (\Exception $exception) {
            $activeSheet->setTitle('Information collection export');
        }

        $activeSheet->fromArray($export->getHeader(), null, 'A1', true);
        $activeSheet->fromArray($export->getContents(), null, 'A2', true);


        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $contentName . '.xls"');
        header('Cache-Control: max-age=0');

        $writer = new Xls($spreadsheet);
        $writer->save('php://output');

        return new Response('');
    }
}
