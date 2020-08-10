<?php

namespace Netgen\Bundle\InformationCollectionBundle\Controller\Admin\Export;

use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Netgen\InformationCollection\API\Service\Exporter;
use Netgen\InformationCollection\API\Value\Export\Export as ExportValue;
use Netgen\InformationCollection\Core\Export\ExportResponseFormatterRegistry;
use eZ\Publish\API\Repository\ContentService;

final class ExportAll extends AbstractController
{
    /**
     * @var \eZ\Publish\API\Repository\ContentService
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

    /**
     * Handles comeplete data export in available formats
     *
     * @param int $contentId
     * @param string $exportIdentifier
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function __invoke($contentId, $exportIdentifier)
    {
        $attribute = new Attribute('infocollector', 'export');
        $this->denyAccessUnlessGranted($attribute);

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
    protected function getExportByContent(Content $content): ExportValue
    {
        $exportCriteria = new ExportCriteria(
            [
                'content' => $content,
            ]
        );

        return $this->exporter->export($exportCriteria);
    }
}
