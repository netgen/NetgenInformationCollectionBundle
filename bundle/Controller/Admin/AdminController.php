<?php

namespace Netgen\Bundle\InformationCollectionBundle\Controller\Admin;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Netgen\Bundle\InformationCollectionBundle\API\Persistence\Anonymizer\Anonymizer;
use Netgen\Bundle\InformationCollectionBundle\API\Service\InformationCollection;
use Netgen\Bundle\InformationCollectionBundle\Core\Pagination\InformationCollectionCollectionListAdapter;
use Netgen\Bundle\InformationCollectionBundle\Core\Pagination\InformationCollectionCollectionListSearchAdapter;
use Netgen\Bundle\InformationCollectionBundle\Core\Pagination\InformationCollectionContentsAdapter;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{
    /**
     * @var InformationCollection
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

    /**
     * @var Anonymizer
     */
    protected $anonymizer;

    public function __construct(
        InformationCollection $service,
        Anonymizer $anonymizer,
        ContentService $contentService,
        ConfigResolverInterface $configResolver
    )
    {
        $this->service = $service;
        $this->contentService = $contentService;
        $this->configResolver = $configResolver;
        $this->anonymizer = $anonymizer;
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
        $contents = $request->request->get('ContentId', []);

        if (empty($contents)) {
            $this->addFlashMessage('errors', 'contents_not_selected');

            return $this->redirectToRoute('netgen_information_collection.route.admin.overview');
        }

        if ($request->request->has('DeleteCollectionByContentAction')) {
            $this->service->deleteCollectionByContent($contents);

            if (count($contents) > 1) {
                $this->addFlashMessage('success', 'contents_removed');
            } else {
                $this->addFlashMessage('success', 'content_removed');
            }

            return $this->redirectToRoute('netgen_information_collection.route.admin.collection_list', ['contentId' => $contentId]);
        }

        $this->addFlashMessage('error', 'something_went_wrong');

        return $this->redirectToRoute('netgen_information_collection.route.admin.overview');
    }

    public function handleCollectionListAction(Request $request)
    {
        $contentId = $request->request->get('ContentId');
        $collections = $request->request->get('CollectionId', []);

        if (empty($collections)) {
            $this->addFlashMessage('errors', 'collections_not_selected');

            return $this->redirectToRoute('netgen_information_collection.route.admin.collection_list', ['contentId' => $contentId]);
        }

        if ($request->request->has('DeleteCollectionAction')) {
            $this->service->deleteCollections($contentId, $collections);

            if (count($collections) > 1) {
                $this->addFlashMessage('success', 'collections_removed');
            } else {
                $this->addFlashMessage('success', 'collection_removed');
            }

            return $this->redirectToRoute('netgen_information_collection.route.admin.collection_list', ['contentId' => $contentId]);
        }

        if ($request->request->has('AnonymizeCollectionAction')) {

            foreach ($collections as $collection) {
                $this->anonymizer->anonymizeCollection($collection);
            }

            if (count($collections) > 1) {
                $this->addFlashMessage('success', 'collections_anonymized');
            } else {
                $this->addFlashMessage('success', 'collection_anonymized');
            }

            return $this->redirectToRoute('netgen_information_collection.route.admin.collection_list', ['contentId' => $contentId]);
        }

        $this->addFlashMessage('error', 'something_went_wrong');

        return $this->redirectToRoute('netgen_information_collection.route.admin.collection_list', ['contentId' => $contentId]);
    }

    public function handleCollectionAction(Request $request)
    {
        $collectionId = $request->request->get('CollectionId');
        $contentId = $request->request->get('ContentId');
        $fields = $request->request->get('FieldId', []);

        if (
            ($request->request->has('AnonymizeFieldAction') || $request->request->has('DeleteFieldAction'))
            && empty($fields)
        ) {
            $this->addFlashMessage('errors', 'fields_not_selected');

            return $this->redirectToRoute('netgen_information_collection.route.admin.view', ['collectionId' => $collectionId]);
        }

        if ($request->request->has('DeleteFieldAction')) {
            $this->service->deleteCollectionFields($contentId, $collectionId, $fields);

            if (count($fields) > 1) {
                $this->addFlashMessage('success', 'fields_removed');
            } else {
                $this->addFlashMessage('success', 'field_removed');
            }

            return $this->redirectToRoute('netgen_information_collection.route.admin.view', ['collectionId' => $collectionId]);
        }

        if ($request->request->has('AnonymizeFieldAction')) {
            $this->anonymizer->anonymizeCollection($collectionId, $fields);

            if (count($fields) > 1) {
                $this->addFlashMessage('success', 'fields_anonymized');
            } else {
                $this->addFlashMessage('success', 'field_anonymized');
            }

            return $this->redirectToRoute('netgen_information_collection.route.admin.view', ['collectionId' => $collectionId]);
        }

        if ($request->request->has('DeleteCollectionAction')) {

            $this->service->deleteCollections($contentId, [$collectionId]);

            $this->addFlashMessage("success", "collection_removed");

            return $this->redirectToRoute('netgen_information_collection.route.admin.collection_list', ['contentId' => $contentId]);

        }

        if ($request->request->has('AnonymizeCollectionAction')) {
            $this->anonymizer->anonymizeCollection($collectionId);

            $this->addFlashMessage("success", "collection_anonymized");

            return $this->redirectToRoute('netgen_information_collection.route.admin.view', ['collectionId' => $collectionId]);
        }

        $this->addFlashMessage('error', 'something_went_wrong');

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
