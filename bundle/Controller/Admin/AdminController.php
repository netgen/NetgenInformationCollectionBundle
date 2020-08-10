<?php

namespace Netgen\Bundle\InformationCollectionBundle\Controller\Admin;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Netgen\InformationCollection\API\Persistence\Anonymizer\Anonymizer;
use Netgen\InformationCollection\API\Service\InformationCollection;
use Netgen\InformationCollection\API\Value\Collection;
use Netgen\InformationCollection\API\Value\Filter\CollectionFields;
use Netgen\InformationCollection\API\Value\Filter\Collections;
use Netgen\InformationCollection\API\Value\Filter\ContentId;
use Netgen\InformationCollection\API\Value\Filter\Contents;
use Netgen\InformationCollection\API\Value\Filter\Query;
use Netgen\InformationCollection\API\Value\Filter\SearchQuery;
use Netgen\InformationCollection\Core\Pagination\InformationCollectionCollectionListAdapter;
use Netgen\InformationCollection\Core\Pagination\InformationCollectionCollectionListSearchAdapter;
use Netgen\InformationCollection\Core\Pagination\InformationCollectionContentsAdapter;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdminController extends Controller
{
    /**
     * @var \Netgen\InformationCollection\API\Service\InformationCollection
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
     * @var \Netgen\InformationCollection\API\Persistence\Anonymizer\Anonymizer
     */
    protected $anonymizer;

    /**
     * @var \Symfony\Contracts\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * AdminController constructor.
     *
     * @param \Netgen\InformationCollection\API\Service\InformationCollection $service
     * @param \Netgen\InformationCollection\API\Persistence\Anonymizer\Anonymizer $anonymizer
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \eZ\Publish\Core\MVC\ConfigResolverInterface $configResolver
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
     */
    public function __construct(
        InformationCollection $service,
        Anonymizer $anonymizer,
        ContentService $contentService,
        ConfigResolverInterface $configResolver,
        TranslatorInterface $translator
    )
    {
        $this->service = $service;
        $this->contentService = $contentService;
        $this->configResolver = $configResolver;
        $this->anonymizer = $anonymizer;
        $this->translator = $translator;
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
        $this->checkReadPermissions();

        $adapter = new InformationCollectionContentsAdapter($this->service, Query::countQuery());
        $pager = $this->getPager($adapter, (int) $request->query->get('page'));

        return $this->render("@NetgenInformationCollection/admin/overview.html.twig", ['objects' => $pager]);
    }

    /**
     * Displays list of collection for selected Content
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \eZ\Publish\API\Repository\Values\Content\Content $content
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function collectionListAction(Request $request, Content $content)
    {
        $this->checkReadPermissions();

        $adapter = new InformationCollectionCollectionListAdapter($this->service, ContentId::withContentId($content->id));
        $pager = $this->getPager($adapter, (int)$request->query->get('page'));

        return $this->render("@NetgenInformationCollection/admin/collection_list.html.twig", [
            'objects' => $pager,
            'content' => $content,
        ]);
    }

    /**
     * Handles collection search
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \eZ\Publish\API\Repository\Values\Content\Content $content
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchAction(Request $request, Content $content)
    {
        $this->checkReadPermissions();

        $query = SearchQuery::withContentAndSearchText($content->id, $request->query->get('searchText'));

        $adapter = new InformationCollectionCollectionListSearchAdapter($this->service, $query);
        $pager = $this->getPager($adapter, (int)$request->query->get('page'));

        return $this->render("@NetgenInformationCollection/admin/collection_list.html.twig",
            [
                'objects' => $pager,
                'content' => $content,
            ]
        );
    }

    /**
     * Displays individual collection details
     *
     * @param \Netgen\InformationCollection\API\Value\Collection $collection
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(Collection $collection)
    {
        $this->checkReadPermissions();

        return $this->render("@NetgenInformationCollection/admin/view.html.twig", [
            'collection' => $collection,
            'content' => $collection->getContent(),
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
        $this->checkReadPermissions();

        $contents = $request->request->get('ContentId', []);
        $count = count($contents);

        if (empty($contents)) {
            $this->addFlashMessage('errors', 'contents_not_selected');

            return $this->redirectToRoute('netgen_information_collection.route.admin.overview');
        }

        if ($request->request->has('DeleteCollectionByContentAction')) {

            $this->checkDeletePermissions();

            $query = new Contents($contents);

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
        $this->checkReadPermissions();

        $contentId = $request->request->get('ContentId');
        $collections = $request->request->get('CollectionId', []);
        $count = count($collections);

        if (empty($collections)) {
            $this->addFlashMessage('errors', 'collections_not_selected');

            return $this->redirectToRoute('netgen_information_collection.route.admin.collection_list', ['contentId' => $contentId]);
        }

        if ($request->request->has('DeleteCollectionAction')) {

            $this->checkDeletePermissions();

            $query = new Collections($contentId, $collections);

            $this->service->deleteCollections($query);

            $this->addFlashMessage('success', 'collection_removed', $count);

            return $this->redirectToRoute('netgen_information_collection.route.admin.collection_list', ['contentId' => $contentId]);
        }

        if ($request->request->has('AnonymizeCollectionAction')) {

            $this->checkAnonymizePermissions();

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
        $this->checkReadPermissions();

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

            $this->checkDeletePermissions();

            $query = new CollectionFields($contentId, $collectionId, $fields);

            $this->service->deleteCollectionFields($query);

            $this->addFlashMessage('success', 'field_removed', $count);

            return $this->redirectToRoute('netgen_information_collection.route.admin.view', ['collectionId' => $collectionId]);
        }

        if ($request->request->has('AnonymizeFieldAction')) {

            $this->checkAnonymizePermissions();

            $this->anonymizer->anonymizeCollection($collectionId, $fields);

            $this->addFlashMessage('success', 'field_anonymized', $count);

            return $this->redirectToRoute('netgen_information_collection.route.admin.view', ['collectionId' => $collectionId]);
        }

        if ($request->request->has('DeleteCollectionAction')) {

            $this->checkDeletePermissions();

            $query = new Collections($contentId, [$collectionId]);
            $this->service->deleteCollections($query);

            $this->addFlashMessage("success", "collection_removed");

            return $this->redirectToRoute('netgen_information_collection.route.admin.collection_list', ['contentId' => $contentId]);

        }

        if ($request->request->has('AnonymizeCollectionAction')) {

            $this->checkAnonymizePermissions();

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
     * @param int $count
     * @param array $parameters
     */
    protected function addFlashMessage(string $messageType, string $message, int $count = 1, array $parameters = array())
    {
        $parameters = array_merge($parameters, ['count' => $count]);

        $this->addFlash(
            'netgen_information_collection.' . $messageType,
            $this->translator->trans(
                $messageType . '.' . $message,
                $parameters,
                'netgen_information_collection_flash'
            )
        );
    }

    /**
     * Returns configured instance of Pagerfanta
     *
     * @param \Pagerfanta\Adapter\AdapterInterface $adapter
     * @param int $currentPage
     *
     * @return \Pagerfanta\Pagerfanta
     */
    protected function getPager(AdapterInterface $adapter, int $currentPage): Pagerfanta
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

    protected function checkReadPermissions(): void
    {
        $attribute = new Attribute('infocollector', 'read');
        $this->denyAccessUnlessGranted($attribute);
    }

    protected function checkDeletePermissions(): void
    {
        $attribute = new Attribute('infocollector', 'delete');
        $this->denyAccessUnlessGranted($attribute);
    }

    protected function checkAnonymizePermissions(): void
    {
        $attribute = new Attribute('infocollector', 'anonymize');
        $this->denyAccessUnlessGranted($attribute);
    }
}
