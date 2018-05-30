<?php

namespace Netgen\Bundle\InformationCollectionBundle\Controller\Admin;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Netgen\Bundle\InformationCollectionBundle\API\Service\InformationCollectionService;
use Netgen\Bundle\InformationCollectionBundle\Core\Pagination\InformationCollectionCollectionListAdapter;
use Netgen\Bundle\InformationCollectionBundle\Core\Pagination\InformationCollectionCollectionListSearchAdapter;
use Netgen\Bundle\InformationCollectionBundle\Core\Pagination\InformationCollectionContentsAdapter;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{
    /**
     * @var InformationCollectionService
     */
    protected $service;

    /**
     * @var ContentService
     */
    protected $contentService;

    /**
     * @var ConfigResolverInterface
     */
    protected $configResolver;

    public function __construct(InformationCollectionService $service, ContentService $contentService, ConfigResolverInterface $configResolver)
    {
        $this->service = $service;
        $this->contentService = $contentService;
        $this->configResolver = $configResolver;
    }

    public function overviewAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ez:infocollector:read');

        $adapter = new InformationCollectionContentsAdapter($this->service);
        $pager = $this->getPager($adapter, (int) $request->query->get('page'));

        return $this->render("NetgenInformationCollectionBundle:admin:overview.html.twig", ['objects' => $pager]);
    }

    public function collectionListAction(Request $request, $contentId)
    {
        $this->denyAccessUnlessGranted('ez:infocollector:read');

        $content = $this->contentService->loadContent($contentId);

        $adapter = new InformationCollectionCollectionListAdapter($this->service, $contentId);
        $pager = $this->getPager($adapter, (int)$request->query->get('page'));

        return $this->render("NetgenInformationCollectionBundle:admin:collection_list.html.twig", [
            'objects' => $pager,
            'content' => $content,
        ]);
    }

    public function searchAction(Request $request, $contentId)
    {
        $this->denyAccessUnlessGranted('ez:infocollector:read');

        $content = $this->contentService->loadContent($contentId);

        $adapter = new InformationCollectionCollectionListSearchAdapter(
            $this->service, $contentId, $request->query->get('searchText')
        );
        $pager = $this->getPager($adapter, (int)$request->query->get('page'));

        return $this->render("NetgenInformationCollectionBundle:admin:collection_list.html.twig",
            [
                'objects' => $pager,
                'content' => $content,
            ]
        );
    }

    public function viewAction($collectionId)
    {
        $this->denyAccessUnlessGranted('ez:infocollector:read');

        $data = $this->service->view($collectionId);

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

        if (empty($request->request->get('CollectionId'))) {
            $this->addFlashMessage('errors', 'netgen_information_collection_admin_flash_collection_missing', array('%tagKeyword%' => 'kifla'));

            return $this->redirectToRoute('netgen_information_collection.route.admin.collection_list', ['contentId' => $contentId]);
        }

        $this->addFlashMessage('success', 'netgen_information_collection_admin_flash_collection_success', array('%tagKeyword%' => 'kifla'));

        return $this->redirectToRoute('netgen_information_collection.route.admin.collection_list', ['contentId' => $contentId]);
    }

    public function handleCollectionAction(Request $request)
    {
        $collectionId = $request->request->get('CollectionId');
        $contentId = $request->request->get('ContentId');

        if (empty($request->request->get('FieldId'))) {
            $this->addFlashMessage('errors', 'fields_not_selected_remove');

            return $this->redirectToRoute('netgen_information_collection.route.admin.view', ['collectionId' => $collectionId]);
        }

        $fields = $request->request->get('FieldId');

        if ($request->request->has('AnonymizeCollectionAction')) {
            $this->addFlashMessage('success', 'fields_removed');
        } else {
            $this->addFlashMessage('errors', 'field_fail', array('%tagKeyword%' => 'kifla'));
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

    /**
     * Returns configured instance of Pagerfanta
     *
     * @param AdapterInterface $adapter
     * @param $currentPage
     *
     * @return Pagerfanta
     */
    protected function getPager(AdapterInterface $adapter, $currentPage)
    {
        $currentPage = (int) $currentPage;
        $pager = new Pagerfanta($adapter);
        $pager->setNormalizeOutOfRangePages(true);
        $pager->setMaxPerPage(
            $this->configResolver->getParameter('admin.max_per_page', 'netgen_information_collection')
        );
        $pager->setCurrentPage($currentPage > 0 ? $currentPage : 1);

        return $pager;
    }

}
