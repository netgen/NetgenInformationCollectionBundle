<?php

namespace Netgen\Bundle\InformationCollectionBundle\Controller\Admin;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Netgen\Bundle\InformationCollectionBundle\API\Persistence\Anonymizer\Anonymizer;
use Netgen\Bundle\InformationCollectionBundle\API\Service\InformationCollection;
use Netgen\Bundle\InformationCollectionBundle\API\Value\InformationCollection\Query;
use Netgen\Bundle\InformationCollectionBundle\Core\Export\ExportResponseFormatterRegistry;
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
     * @var \Netgen\Bundle\InformationCollectionBundle\Core\Export\ExportResponseFormatterRegistry
     */
    protected $formatterRegistry;

    /**
     * AdminController constructor.
     *
     * @param \Netgen\Bundle\InformationCollectionBundle\API\Service\InformationCollection $service
     * @param \Netgen\Bundle\InformationCollectionBundle\API\Persistence\Anonymizer\Anonymizer $anonymizer
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \eZ\Publish\Core\MVC\ConfigResolverInterface $configResolver
     * @param \Netgen\Bundle\InformationCollectionBundle\Core\Export\ExportResponseFormatterRegistry $formatterRegistry
     */
    public function __construct(
        InformationCollection $service,
        Anonymizer $anonymizer,
        ContentService $contentService,
        ConfigResolverInterface $configResolver,
        ExportResponseFormatterRegistry $formatterRegistry
    )
    {
        $this->service = $service;
        $this->contentService = $contentService;
        $this->configResolver = $configResolver;
        $this->anonymizer = $anonymizer;
        $this->formatterRegistry = $formatterRegistry;
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

        $adapter = new InformationCollectionContentsAdapter($this->service, Query::count());
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
        $query = Query::withContent($contentId);
        $adapter = new InformationCollectionCollectionListAdapter($this->service, $query);
        $pager = $this->getPager($adapter, (int)$request->query->get('page'));

        return $this->render("NetgenInformationCollectionBundle:admin:collection_list.html.twig", [
            'objects' => $pager,
            'content' => $content,
            'formatters' => $this->formatterRegistry->getExportResponseFormatters(),
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
                'formatters' => $this->formatterRegistry->getExportResponseFormatters(),
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
     * Edits the collection with provided ID
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $collectionId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, int $collectionId)
    {
        $this->denyAccessUnlessGranted('ez:infocollector:edit');

        $collection = $this->service->getCollection(new Query(['collectionId' => $collectionId]));

        /** @var \eZ\Publish\API\Repository\LocationService $locationService */
        $locationService = $this->container
            ->get('ezpublish.api.service.location');

        /** @var \Netgen\Bundle\InformationCollectionBundle\Form\Builder\FormBuilder $formBuilder */
        $formBuilder = $this->container
            ->get('netgen_information_collection.form.builder');

        /** @var \Netgen\Bundle\InformationCollectionBundle\Factory\LegacyDataFactoryInterface $factory */
        $factory = $this->container
            ->get('netgen_information_collection.factory.field_data');

        /** @var \Netgen\Bundle\InformationCollectionBundle\Repository\EzInfoCollectionRepository $infoCollectionRepository */
        $infoCollectionRepository = $this->container
            ->get('netgen_information_collection.repository.ez_info_collection');

        /** @var \Netgen\Bundle\InformationCollectionBundle\Repository\EzInfoCollectionAttributeRepository $infoCollectionRepository */
        $infoCollectionAttributeRepository = $this->container
            ->get('netgen_information_collection.repository.ez_info_collection_attribute');

        $location = $locationService->loadLocation($collection->content->contentInfo->mainLocationId);
        $form = $formBuilder->createUpdateFormForLocation($location, $collection)->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \Netgen\Bundle\InformationCollectionBundle\Form\Payload\InformationCollectionStruct $struct */
            $struct = $form->getData()->payload;

            /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType */
            $contentType = $form->getData()->definition;

            $ezInfo = $infoCollectionRepository->find($collectionId);
            $ezInfo->setModified(time());

            $infoCollectionRepository->save($ezInfo);

            /**
             * @var \eZ\Publish\Core\FieldType\Value $value
             */
            foreach ($struct->getCollectedFields() as $fieldDefIdentifier => $value) {
                if ($value === null) {
                    continue;
                }

                $fieldDefinition = $contentType->getFieldDefinition($fieldDefIdentifier);

                /* @var \Netgen\Bundle\InformationCollectionBundle\Value\LegacyData $legacyValue */
                $legacyValue = $factory->getLegacyValue($value, $fieldDefinition);

                $ezInfoAttributes = $infoCollectionAttributeRepository->findByCollectionIdAndFieldDefinitionIds(
                    $collectionId,
                    [$fieldDefinition->id]
                );

                if (count($ezInfoAttributes) > 0) {
                    $ezInfoAttribute = $ezInfoAttributes[0];
                } else {
                    $ezInfoAttribute = $infoCollectionAttributeRepository->getInstance();
                    $ezInfoAttribute->setContentObjectId($collection->content->id);
                    $ezInfoAttribute->setContentObjectAttributeId($collection->content->getField($fieldDefinition->identifier)->id);
                    $ezInfoAttribute->setContentClassAttributeId($fieldDefinition->id);
                    $ezInfoAttribute->setInformationCollectionId($collection->entity->getId());
                }

                $ezInfoAttribute->setDataInt($legacyValue->getDataInt());
                $ezInfoAttribute->setDataFloat($legacyValue->getDataFloat());
                $ezInfoAttribute->setDataText($legacyValue->getDataText());

                $infoCollectionAttributeRepository->save($ezInfoAttribute);
            }

            return $this->redirectToRoute(
                'netgen_information_collection.route.admin.view',
                [
                    'contentId' => $location->contentInfo->id,
                    'collectionId' => $collection->entity->getId(),
                ]
            );
        }

        return $this->render("NetgenInformationCollectionBundle:admin:edit.html.twig", [
            'collection' => $collection,
            'content' => $location->getContent(),
            'form' => $form->createView(),
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
        $this->denyAccessUnlessGranted('ez:infocollector:read');

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
        $this->denyAccessUnlessGranted('ez:infocollector:read');

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
        $this->denyAccessUnlessGranted('ez:infocollector:read');

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
