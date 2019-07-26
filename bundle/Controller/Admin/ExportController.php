<?php

namespace Netgen\Bundle\InformationCollectionBundle\Controller\Admin;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
use eZ\Bundle\EzPublishCoreBundle\Controller;
use Netgen\InformationCollection\API\Service\Exporter;
use Netgen\InformationCollection\API\Value\Export\ExportCriteria;
use Netgen\InformationCollection\Form\Type\ExportType;
use Symfony\Component\HttpFoundation\Request;
use League\Csv\Writer;
use SplTempFileObject;
use Symfony\Component\HttpFoundation\Response;

final class ExportController extends Controller
{
    /**
     * @var \Netgen\InformationCollection\API\Service\Exporter
     */
    protected $exporter;

    /**
     * \Netgen\InformationCollection\API\Service\Exporter constructor.
     *
     * @param Exporter $exporter
     */
    public function __construct(Exporter $exporter)
    {
        $this->exporter = $exporter;
    }

    /**
     * Handles export
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Content $content
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function exportAction(Content $content, Request $request): Response
    {
        $attribute = new Attribute('infocollector', 'export');
        $this->denyAccessUnlessGranted($attribute);

        $form = $this->createForm(ExportType::class);
        $form->handleRequest($request);

        if ($form->get('cancel')->isClicked()) {
            return $this->redirect($this->generateUrl('netgen_information_collection.route.admin.overview'));
        }

        if ($form->isValid() && $form->get('export')->isClicked()) {

            $exportCriteria = new ExportCriteria(
                $content,
                $form->getData()['dateFrom'],
                $form->getData()['dateTo'],
                $form->getData()['offset'],
                $form->getData()['limit']
            );

            $export = $this->exporter->export($exportCriteria);

            $writer = Writer::createFromFileObject(new SplTempFileObject());
            $writer->setDelimiter(",");
            $writer->setNewline("\r\n"); //use windows line endings for compatibility with some csv libraries
            $writer->setOutputBOM(Writer::BOM_UTF8); //adding the BOM sequence on output
            $writer->insertOne($export->getHeader());
            $writer->insertAll($export->getContents());

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
