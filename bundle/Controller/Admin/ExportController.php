<?php

namespace Netgen\Bundle\InformationCollectionBundle\Controller\Admin;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\Content;
use Netgen\Bundle\InformationCollectionBundle\API\Service\Exporter;
use Netgen\Bundle\InformationCollectionBundle\API\Value\Export\ExportCriteria;
use Netgen\Bundle\InformationCollectionBundle\Core\Export\ExportResponseFormatterRegistry;
use Netgen\Bundle\InformationCollectionBundle\Form\Type\ExportType;
use Symfony\Component\HttpFoundation\Request;
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
     * @var \Netgen\Bundle\InformationCollectionBundle\Core\Export\ExportResponseFormatterRegistry
     */
    protected $formatterRegistry;

    public function __construct(
        ContentService $contentService,
        Exporter $exporter,
        ExportResponseFormatterRegistry $formatterRegistry
    )
    {
        $this->contentService = $contentService;
        $this->exporter = $exporter;
        $this->formatterRegistry = $formatterRegistry;
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

            $formatter = $this->formatterRegistry->getExportResponseFormatter(
                $form->getData()['exportType']
            );

            return $formatter->format($export, $content);
        }

        return $this->render("@NetgenInformationCollection/admin/export_menu.html.twig",
            [
                'content' => $content,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Handles comeplete data export in available formats
     *
     * @param int $contentId
     * @param string $exportIdentifier
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function exportAllAction($contentId, $exportIdentifier)
    {
        $this->denyAccessUnlessGranted('ez:infocollector:read');

        $formatter = $this->formatterRegistry->getExportResponseFormatter($exportIdentifier);

        $content = $this->contentService->loadContent($contentId);
        $export = $this->getExportByContent($content);

        return $formatter->format($export, $content);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Content $content
     *
     * @return \Netgen\Bundle\InformationCollectionBundle\API\Value\Export\Export
     */
    protected function getExportByContent(Content $content)
    {
        $exportCriteria = new ExportCriteria(
            [
                'content' => $content,
            ]
        );

        return $this->exporter->exportAll($exportCriteria);
    }
}
