<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Export;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Core\Helper\TranslationHelper;
use Netgen\InformationCollection\API\Export\ExportResponseFormatter;
use Netgen\InformationCollection\API\Value\Export\Export;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use Symfony\Component\HttpFoundation\Response;
use function header;
use function mb_substr;

final class XlsExportResponseFormatter implements ExportResponseFormatter
{
    private TranslationHelper $translationHelper;

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
            $activeSheet->setTitle(mb_substr($contentName, 0, 30));
        } catch (Exception $exception) {
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
