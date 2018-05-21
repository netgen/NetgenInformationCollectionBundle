<?php

namespace Netgen\Bundle\InformationCollectionBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use eZ\Bundle\EzPublishCoreBundle\Controller;
use Netgen\Bundle\InformationCollectionBundle\Core\Pagination\InformationCollectionCollectionListAdapter;
use Netgen\Bundle\InformationCollectionBundle\Core\Pagination\InformationCollectionCollectionListSearchAdapter;
use Netgen\Bundle\InformationCollectionBundle\Core\Pagination\InformationCollectionContentsAdapter;
use Netgen\Bundle\InformationCollectionBundle\Repository\EzInfoCollectionRepository;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    public function overviewAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ez:infocollector:read');

        $service = $this->container->get('netgen_information_collection.api.service');

        $currentPage = (int) $request->query->get('page');
        $adapter = new InformationCollectionContentsAdapter($service);
        $pager = new Pagerfanta($adapter);
        $pager->setNormalizeOutOfRangePages(true);
        $pager->setMaxPerPage(10);
        $pager->setCurrentPage($currentPage > 0 ? $currentPage : 1);

        return $this->render("NetgenInformationCollectionBundle:admin:overview.html.twig", ['objects' => $pager]);
    }

    public function collectionListAction(Request $request, $contentId)
    {
        $this->denyAccessUnlessGranted('ez:infocollector:read');

        $content = $this->container->get('ezpublish.api.service.content')
            ->loadContent($contentId);

        $service = $this->container->get('netgen_information_collection.api.service');

        $currentPage = (int)$request->query->get('page');
        $adapter = new InformationCollectionCollectionListAdapter($service, $contentId);
        $pager = new Pagerfanta($adapter);
        $pager->setNormalizeOutOfRangePages(true);
        $pager->setMaxPerPage(10);
        $pager->setCurrentPage($currentPage > 0 ? $currentPage : 1);

        return $this->render("NetgenInformationCollectionBundle:admin:collection_list.html.twig", [
            'objects' => $pager,
            'content' => $content,
        ]);
    }

    public function searchAction(Request $request, $contentId)
    {
        $this->denyAccessUnlessGranted('ez:infocollector:read');
        
        $content = $this->container->get('ezpublish.api.service.content')
            ->loadContent($contentId);

        $service = $this->container->get('netgen_information_collection.api.service');

        $currentPage = (int)$request->query->get('page');
        $adapter = new InformationCollectionCollectionListSearchAdapter(
            $service, $contentId, $request->query->get('searchText')
        );
        $pager = new Pagerfanta($adapter);
        $pager->setNormalizeOutOfRangePages(true);
        $pager->setMaxPerPage(10);
        $pager->setCurrentPage($currentPage > 0 ? $currentPage : 1);

        return $this->render("NetgenInformationCollectionBundle:admin:collection_list.html.twig",
            [
                'objects' => $pager,
                'content' => $content,
            ]
        );
    }

    public function viewAction($collectionId)
    {
        $repository = $this->container->get('netgen_information_collection.api.service');
        $data = $repository->view($collectionId);

        return $this->render("NetgenInformationCollectionBundle:admin:view.html.twig", $data);
    }

    public function handleContentsAction(Request $request)
    {
        $this->addFlashMessage('success', 'netgen_information_collection_admin_flash_contents_success', array('%tagKeyword%' => 'kifla'));

        return $this->redirectToRoute('netgen_information_collection.route.admin.overview');
    }

    public function handleCollectionListAction(Request $request)
    {
        $contentId = $request->request->get('ContentId');

        $this->addFlashMessage('success', 'netgen_information_collection_admin_flash_collection_success', array('%tagKeyword%' => 'kifla'));

        return $this->redirectToRoute('netgen_information_collection.route.admin.collection_list', ['contentId' => $contentId]);
    }

    public function handleCollectionAction(Request $request)
    {
        $collectionId = $request->request->get('CollectionId');

        if ($request->request->has('AnonymizeCollectionAction')) {
            $this->addFlashMessage('success', 'netgen_information_collection_admin_flash_field_success', array('%tagKeyword%' => 'kifla'));
        } else {
            $this->addFlashMessage('errors', 'netgen_information_collection_admin_flash_field_fail', array('%tagKeyword%' => 'kifla'));
        }


        return $this->redirectToRoute('netgen_information_collection.route.admin.view', ['collectionId' => $collectionId]);
    }

    /**
     * Adds a flash message with specified parameters.
     *
     * @param string $messageType
     * @param string $message
     * @param array $parameters
     */
    protected function addFlashMessage($messageType, $message, array $parameters = array())
    {
        $this->addFlash(
            'netgen_information_collection.' . $messageType,
            $this->container->get('translator')->trans(
                $messageType . '.' . $message,
                $parameters,
                'netgen_information_collection_flash'
            )
        );
    }
}