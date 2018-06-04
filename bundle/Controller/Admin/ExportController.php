<?php

namespace Netgen\Bundle\InformationCollectionBundle\Controller\Admin;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use Netgen\Bundle\InformationCollectionBundle\API\Service\Exporter;
use Netgen\Bundle\InformationCollectionBundle\API\Value\Export\ExportCriteria;
use Netgen\Bundle\InformationCollectionBundle\Form\Type\ExportType;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use League\Csv\Writer;
use SplTempFileObject;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

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

    public function __construct(ContentService $contentService, Exporter $exporter)
    {
        $this->contentService = $contentService;
        $this->exporter = $exporter;
    }

    public function exportAction($contentId, Request $request)
    {
        $this->denyAccessUnlessGranted('ez:infocollector:read');

        $content = $this->contentService->loadContent($contentId);

        $form = $this->createForm(ExportType::class);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $exportCriteria = new ExportCriteria(
                [
                    'content' => $content,
                    'from' => $form->getData('dateFrom'),
                    'to' => $form->getData('dateTo'),
                ]
            );

            $export = $this->exporter->export($exportCriteria);

            $writer = Writer::createFromFileObject(new SplTempFileObject());
            $writer->setDelimiter("\t"); //the delimiter will be the tab character
            $writer->setNewline("\r\n"); //use windows line endings for compatibility with some csv libraries
            $writer->setOutputBOM(Writer::BOM_UTF8); //adding the BOM sequence on output
            $writer->insertOne($export->header);
            $writer->insertAll($export->contents);

            $writer->output('export.csv');
            return new Response('');
        }

        return $this->render("@NetgenInformationCollection/admin/export_menu.html.twig",
            [
                'content' => $content,
                'form' => $form->createView(),
            ]
        );
    }
}
