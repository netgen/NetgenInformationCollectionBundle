<?php

namespace Netgen\Bundle\InformationCollectionBundle\Controller\Admin;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\Core\Helper\TranslationHelper;
use Netgen\Bundle\InformationCollectionBundle\API\Service\Exporter;
use Netgen\Bundle\InformationCollectionBundle\API\Value\Export\Export;
use Netgen\Bundle\InformationCollectionBundle\API\Value\Export\ExportCriteria;
use Netgen\Bundle\InformationCollectionBundle\Form\Type\ExportType;
use Symfony\Component\HttpFoundation\Request;
use League\Csv\Writer;
use SplTempFileObject;
use PHPExcel;
use PHPExcel_IOFactory;
use Symfony\Component\HttpFoundation\Response;

class ExportController extends Controller
{
    /**
     * @var \eZ\Publish\API\Repository\ContentService
     */
    protected $contentService;

    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\API\Service\Exporter
     */
    protected $exporter;

    /**
     * @var \eZ\Publish\Core\Helper\TranslationHelper
     */
    protected $translationHelper;

    /**
     * @var array
     */
    protected $exportConfiguration;

    public function __construct(
        ContentService $contentService,
        Exporter $exporter,
        TranslationHelper $translationHelper,
        array $exportConfiguration
    )
    {
        $this->contentService = $contentService;
        $this->exporter = $exporter;
        $this->translationHelper = $translationHelper;
        $this->exportConfiguration = $exportConfiguration;
    }

    /**
     * Handles export
     *
     * @param int $contentId
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function exportAction($contentId, Request $request)
    {
        $this->denyAccessUnlessGranted('ez:infocollector:read');

        $content = $this->contentService->loadContent($contentId);

        $form = $this->createForm(ExportType::class);
        $form->handleRequest($request);

        if ($form->get('cancel')->isClicked()) {
            return $this->redirect($this->generateUrl('netgen_information_collection.route.admin.collection_list', ['contentId' => $contentId]));
        }

        if ($form->isValid() && $form->get('export')->isClicked()) {

            $exportCriteria = new ExportCriteria(
                [
                    'content' => $content,
                    'from' => $form->getData()['dateFrom'],
                    'to' => $form->getData()['dateTo'],
                ]
            );

            $export = $this->exporter->export($exportCriteria);

            if ($form->getData()['exportType'] === 'csv') {
                return $this->createCsvResponse($export, $content);
            }

            return $this->createXlsResponse($export, $content);
        }

        return $this->render("@NetgenInformationCollection/admin/export_menu.html.twig",
            [
                'content' => $content,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Handles data export in CSV format
     *
     * @param int $contentId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function exportCsvAction($contentId)
    {
        $this->denyAccessUnlessGranted('ez:infocollector:read');

        $content = $this->contentService->loadContent($contentId);
        $export = $this->getExportByContent($content);

        return $this->createCsvResponse($export, $content);
    }

    /**
     * Handles data export in XLS format
     *
     * @param int $contentId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function exportXlsAction($contentId)
    {
        $this->denyAccessUnlessGranted('ez:infocollector:read');

        $content = $this->contentService->loadContent($contentId);
        $export = $this->getExportByContent($content);

        return $this->createXlsResponse($export, $content);
    }

    protected function getExportByContent(Content $content)
    {
        $exportCriteria = new ExportCriteria(
            [
                'content' => $content,
            ]
        );

        return $this->exporter->exportAll($exportCriteria);
    }

    protected function createCsvResponse(Export $export, Content $content)
    {
        $contentName = $this->translationHelper->getTranslatedContentName($content);

        $writer = Writer::createFromFileObject(new SplTempFileObject());
        $writer->setDelimiter($this->exportConfiguration['delimiter']);
        $writer->setEnclosure($this->exportConfiguration['enclosure']);
        $writer->setNewline($this->exportConfiguration['newline']);
        $writer->setOutputBOM(Writer::BOM_UTF8); //adding the BOM sequence on output
        $writer->insertOne($export->header);
        $writer->insertAll($export->contents);

        $writer->output($contentName . '.csv');
        return new Response('');
    }

    protected function createXlsResponse(Export $export, Content $content)
    {
        $contentName = $this->translationHelper->getTranslatedContentName($content);

        array_unshift($export->contents, $export->header);

        $excel = new PHPExcel();
        $excel->setActiveSheetIndex(0);

        $activeSheet = $excel->getActiveSheet();
        $activeSheet->setTitle($contentName);

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
