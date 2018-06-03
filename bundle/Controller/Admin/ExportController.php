<?php

namespace Netgen\Bundle\InformationCollectionBundle\Controller\Admin;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use Netgen\Bundle\InformationCollectionBundle\API\Service\Exporter;
use Netgen\Bundle\InformationCollectionBundle\API\Value\Export\ExportCriteria;
use Netgen\Bundle\InformationCollectionBundle\Form\Type\ExportType;
use Symfony\Component\HttpFoundation\Request;

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
     * @var \eZ\Publish\API\Repository\ContentTypeService
     */
    protected $contentTypeService;

    public function __construct(ContentService $contentService, ContentTypeService $contentTypeService, Exporter $exporter)
    {
        $this->contentService = $contentService;
        $this->exporter = $exporter;
        $this->contentTypeService = $contentTypeService;
    }

    public function exportAction($contentId, Request $request)
    {
        $this->denyAccessUnlessGranted('ez:infocollector:read');

        $content = $this->contentService->loadContent($contentId);
        $contentType = $this->contentTypeService->loadContentType($content->contentInfo->contentTypeId);

        $form = $this->createForm(ExportType::class);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $exportCriteria = new ExportCriteria(
                [
                    'content' => $content,
                    'contentType' => $contentType,
                    'from' => $form->getData('dateFrom'),
                    'to' => $form->getData('dateTo'),
                ]
            );

            $export = $this->exporter->export($exportCriteria);

        }

        return $this->render("@NetgenInformationCollection/admin/export_menu.html.twig",
            [
                'content' => $content,
                'form' => $form->createView(),
            ]
        );
    }
}
