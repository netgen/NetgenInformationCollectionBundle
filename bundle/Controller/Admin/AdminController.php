<?php

namespace Netgen\Bundle\InformationCollectionBundle\Controller\Admin;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Netgen\Bundle\InformationCollectionBundle\API\Persistence\Anonymizer\Anonymizer;
use Netgen\Bundle\InformationCollectionBundle\API\Service\InformationCollection;
use Netgen\Bundle\InformationCollectionBundle\API\Value\Export\InformationCollection\Query;
use Netgen\Bundle\InformationCollectionBundle\Core\Pagination\InformationCollectionCollectionListAdapter;
use Netgen\Bundle\InformationCollectionBundle\Core\Pagination\InformationCollectionCollectionListSearchAdapter;
use Netgen\Bundle\InformationCollectionBundle\Core\Pagination\InformationCollectionContentsAdapter;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{
    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\API\Service\InformationCollection
     */
    protected $service;

    /**
     * @var \eZ\Publish\API\Repository\ContentService
     */
    protected $contentService;

    /**
     * @var \eZ\Publish\Core\MVC\ConfigResolverInterface
     */
    protected $configResolver;

    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\API\Persistence\Anonymizer\Anonymizer
     */
    protected $anonymizer;

    /**
     * AdminController constructor.
     *
     * @param \Netgen\Bundle\InformationCollectionBundle\API\Service\InformationCollection $service
     * @param \Netgen\Bundle\InformationCollectionBundle\API\Persistence\Anonymizer\Anonymizer $anonymizer
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \eZ\Publish\Core\MVC\ConfigResolverInterface $configResolver
     */
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

    /**
     * Displays overview page
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function overviewAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ez:infocollector:read');

        $adapter = new InformationCollectionContentsAdapter($this->service, new Query());
        $pager = $this->getPager($adapter, (int) $request->query->get('page'));

        return $this->render("NetgenInformationCollectionBundle:admin:overview.html.twig", ['objects' => $pager]);
    }

    /**
     * Displays list of collection for selected Content
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $contentId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function collectionListAction(Request $request, $contentId)
    {
        $this->denyAccessUnlessGranted('ez:infocollector:read');

        $content = $this->contentService->loadContent($contentId);
        $query = new Query([
            'contentId' => $contentId,
        ]);
        $adapter = new InformationCollectionCollectionListAdapter($this->service, $query);
        $pager = $this->getPager($adapter, (int)$request->query->get('page'));

        return $this->render("NetgenInformationCollectionBundle:admin:collection_list.html.twig", [
            'objects' => $pager,
            'content' => $content,
        ]);
    }

    /**
     * Handles collection search
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $contentId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchAction(Request $request, $contentId)
    {
        $this->denyAccessUnlessGranted('ez:infocollector:read');

        $content = $this->contentService->loadContent($contentId);

        $query = new Query([
            'contentId' => $contentId,
            'searchText' => $request->query->get('searchText'),
        ]);
        $adapter = new InformationCollectionCollectionListSearchAdapter($this->service, $query);
        $pager = $this->getPager($adapter, (int)$request->query->get('page'));

        return $this->render("NetgenInformationCollectionBundle:admin:collection_list.html.twig",
            [
                'objects' => $pager,
                'content' => $content,
            ]
        );
    }

    /**
     * Displays individual collection details
     *
     * @param int $collectionId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction($collectionId)
    {
        $this->denyAccessUnlessGranted('ez:infocollector:read');

        $collection = $this->service->getCollection(new Query(['collectionId' => $collectionId]));

        return $this->render("NetgenInformationCollectionBundle:admin:view.html.twig", [
            'collection' => $collection,
            'content' => $collection->content,
        ]);
    }

    /**
     * Handles actions performed on overview page
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function handleContentsAction(Request $request)
    {
        $contents = $request->request->get('ContentId', []);
        $count = count($contents);

        if (empty($contents)) {
            $this->addFlashMessage('errors', 'contents_not_selected');

            return $this->redirectToRoute('netgen_information_collection.route.admin.overview');
        }

        if ($request->request->has('DeleteCollectionByContentAction')) {
            $query = new Query([
                'contents' => $contents,
            ]);
            $this->service->deleteCollectionByContent($query);

            $this->addFlashMessage('success', 'content_removed', $count);

            return $this->redirectToRoute('netgen_information_collection.route.admin.overview');
        }

        $this->addFlashMessage('error', 'something_went_wrong');

        return $this->redirectToRoute('netgen_information_collection.route.admin.overview');
    }

    /**
     * Handles actions performed on collection list page
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function handleCollectionListAction(Request $request)
    {
        $contentId = $request->request->get('ContentId');
        $collections = $request->request->get('CollectionId', []);
        $count = count($collections);

        if (empty($collections)) {
            $this->addFlashMessage('errors', 'collections_not_selected');

            return $this->redirectToRoute('netgen_information_collection.route.admin.collection_list', ['contentId' => $contentId]);
        }

        if ($request->request->has('DeleteCollectionAction')) {
            $query = new Query([
                'contentId' => $contentId,
                'collections' => $collections,
            ]);
            $this->service->deleteCollections($query);

            $this->addFlashMessage('success', 'collection_removed', $count);

            return $this->redirectToRoute('netgen_information_collection.route.admin.collection_list', ['contentId' => $contentId]);
        }

        if ($request->request->has('AnonymizeCollectionAction')) {

            foreach ($collections as $collection) {
                $this->anonymizer->anonymizeCollection($collection);
            }

            $this->addFlashMessage('success', 'collection_anonymized', $count);

            return $this->redirectToRoute('netgen_information_collection.route.admin.collection_list', ['contentId' => $contentId]);
        }

        $this->addFlashMessage('error', 'something_went_wrong');

        return $this->redirectToRoute('netgen_information_collection.route.admin.collection_list', ['contentId' => $contentId]);
    }

    /**
     * Handles action on collection details page
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function handleCollectionAction(Request $request)
    {
        $collectionId = $request->request->get('CollectionId');
        $contentId = $request->request->get('ContentId');
        $fields = $request->request->get('FieldId', []);
        $count = count($fields);

        if (
            ($request->request->has('AnonymizeFieldAction') || $request->request->has('DeleteFieldAction'))
            && empty($fields)
        ) {
            $this->addFlashMessage('errors', 'fields_not_selected');

            return $this->redirectToRoute('netgen_information_collection.route.admin.view', ['collectionId' => $collectionId]);
        }

        if ($request->request->has('DeleteFieldAction')) {
            $query = new Query([
                'contentId' => $contentId,
                'collectionId' => $collectionId,
                'fields' => $fields,
            ]);
            $this->service->deleteCollectionFields($query);

            $this->addFlashMessage('success', 'field_removed', $count);

            return $this->redirectToRoute('netgen_information_collection.route.admin.view', ['collectionId' => $collectionId]);
        }

        if ($request->request->has('AnonymizeFieldAction')) {
            $this->anonymizer->anonymizeCollection($collectionId, $fields);

            $this->addFlashMessage('success', 'field_anonymized', $count);

            return $this->redirectToRoute('netgen_information_collection.route.admin.view', ['collectionId' => $collectionId]);
        }

        if ($request->request->has('DeleteCollectionAction')) {
            $query = new Query([
                'contentId' => $contentId,
                'collections' => [$collectionId],
            ]);
            $this->service->deleteCollections($query);

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
     * @param string $count
     * @param array $parameters
     */
    protected function addFlashMessage($messageType, $message, $count = 1, array $parameters = array())
    {
        $this->addFlash(
            'netgen_information_collection.' . $messageType,
            $this->container->get('translator')->transChoice(
                $messageType . '.' . $message,
                $count,
                $parameters,
                'netgen_information_collection_flash'
            )
        );
    }

    /**
     * Returns configured instance of Pagerfanta
     *
     * @param \Pagerfanta\Adapter\AdapterInterface $adapter
     * @param $currentPage
     *
     * @return \Pagerfanta\Pagerfanta
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
