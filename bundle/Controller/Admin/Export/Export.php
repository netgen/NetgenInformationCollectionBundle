<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Controller\Admin\Export;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Core\MVC\Symfony\Security\Authorization\Attribute;
use Netgen\Bundle\InformationCollectionBundle\Form\ExportType;
use Netgen\InformationCollection\API\Service\Exporter;
use Netgen\InformationCollection\API\Value\Export\ExportCriteria;
use Netgen\InformationCollection\Core\Export\ExportResponseFormatterRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class Export extends AbstractController
{
    protected ContentService $contentService;

    protected Exporter $exporter;

    protected ExportResponseFormatterRegistry $formatterRegistry;

    public function __construct(
        ContentService $contentService,
        Exporter $exporter,
        ExportResponseFormatterRegistry $formatterRegistry
    ) {
        $this->contentService = $contentService;
        $this->exporter = $exporter;
        $this->formatterRegistry = $formatterRegistry;
    }

    /**
     * @param int $contentId
     * @param Request $request
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     *
     * @return Response
     */
    public function __invoke($contentId, Request $request): Response
    {
        $attribute = new Attribute('infocollector', 'export');
        $this->denyAccessUnlessGranted($attribute);

        $content = $this->contentService->loadContent((int) $contentId);

        $form = $this->createForm(ExportType::class, null, [
            'contentId' => $content->id,
        ]);
        $form->handleRequest($request);

        if ($form->get('cancel')->isClicked()) {
            return $this->redirect(
                $this->generateUrl(
                    'netgen_information_collection.route.admin.collection_list',
                    [
                        'contentId' => $contentId,
                    ]
                )
            );
        }

        if ($form->isSubmitted() && $form->isValid() && $form->get('export')->isClicked()) {
            /** @var ExportCriteria $data */
            $data = $form->getData();
            $export = $this->exporter->export($data);

            $formatter = $this->formatterRegistry->getExportResponseFormatter($data->getExportIdentifier());

            return $formatter->format($export, $content);
        }

        return $this->render(
            '@NetgenInformationCollection/admin/export_menu.html.twig',
            [
                'content' => $content,
                'form' => $form->createView(),
            ]
        );
    }
}
