<?php

namespace Netgen\Bundle\InformationCollectionBundle\Controller\Admin\Export;

use Ibexa\Core\MVC\Symfony\Security\Authorization\Attribute;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Netgen\InformationCollection\API\Value\Export\ExportCriteria;
use Netgen\InformationCollection\API\Service\Exporter;
use Netgen\InformationCollection\Core\Export\ExportResponseFormatterRegistry;
use Symfony\Component\HttpFoundation\Request;
use Netgen\InformationCollection\Form\Type\ExportType;
use Ibexa\Contracts\Core\Repository\ContentService;

final class Export extends AbstractController
{
    /**
     * @var \Ibexa\Contracts\Core\Repository\ContentService
     */
    protected $contentService;

    /**
     * @var \Netgen\InformationCollection\API\Service\Exporter
     */
    protected $exporter;

    /**
     * @var \Netgen\InformationCollection\Core\Export\ExportResponseFormatterRegistry
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

    public function __invoke($contentId, Request $request)
    {
        $attribute = new Attribute('infocollector', 'export');
        $this->denyAccessUnlessGranted($attribute);

        $content = $this->contentService->loadContent($contentId);

        $form = $this->createForm(ExportType::class, null, [
            'contentId' => $content->id,
        ]);
        $form->handleRequest($request);

        if ($form->get('cancel')->isClicked()) {
            return $this->redirect($this->generateUrl('netgen_information_collection.route.admin.collection_list', ['contentId' => $contentId]));
        }

        if ($form->get('export')->isClicked() && $form->isSubmitted() && $form->isValid()) {

            /** @var ExportCriteria $data */
            $data = $form->getData();
            $export = $this->exporter->export($data);

            $formatter = $this->formatterRegistry->getExportResponseFormatter($data->getExportIdentifier());

            return $formatter->format($export, $content);
        }

        return $this->render("@NetgenInformationCollection/admin/export_menu.html.twig",
            [
                'content' => $content,
                'form' => $form->createView(),
            ]
        );
    }
}
