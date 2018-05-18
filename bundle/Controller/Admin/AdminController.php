<?php

namespace Netgen\Bundle\InformationCollectionBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use eZ\Publish\Core\MVC\Symfony\Controller\Controller;
use Netgen\Bundle\InformationCollectionBundle\Core\Pagination\InformationCollectionCollectionListAdapter;
use Netgen\Bundle\InformationCollectionBundle\Core\Pagination\InformationCollectionContentsAdapter;
use Netgen\Bundle\InformationCollectionBundle\Repository\EzInfoCollectionRepository;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{
    public function overviewAction(Request $request)
    {
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
        $content = $this->container->get('ezpublish.api.service.content')
            ->loadContent($contentId);

        $service = $this->container->get('netgen_information_collection.api.service');

        $currentPage = (int) $request->query->get('page');
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

    public function viewAction($collectionId)
    {
        $repository = $this->container->get('netgen_information_collection.api.service');
        $data = $repository->view($collectionId);

        return $this->render("NetgenInformationCollectionBundle:admin:view.html.twig", $data);
    }
}