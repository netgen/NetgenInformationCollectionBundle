<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Controller\Admin;

use Doctrine\ORM\NonUniqueResultException;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use Ibexa\Bundle\Core\Controller;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\Security\Authorization\Attribute;
use Netgen\Bundle\IbexaFormsBundle\Form\Payload\InformationCollectionStruct;
use Netgen\Bundle\InformationCollectionBundle\Form\Builder\FormBuilder;
use Netgen\InformationCollection\API\Persistence\Anonymizer\Anonymizer;
use Netgen\InformationCollection\API\Service\InformationCollection;
use Netgen\InformationCollection\API\Value\Collection;
use Netgen\InformationCollection\API\Value\Filter\CollectionFields;
use Netgen\InformationCollection\API\Value\Filter\CollectionId;
use Netgen\InformationCollection\API\Value\Filter\Collections;
use Netgen\InformationCollection\API\Value\Filter\ContentId;
use Netgen\InformationCollection\API\Value\Filter\Contents;
use Netgen\InformationCollection\API\Value\Filter\Query;
use Netgen\InformationCollection\API\Value\Filter\SearchQuery;
use Netgen\InformationCollection\Core\Factory\FieldDataFactory;
use Netgen\InformationCollection\Core\Pagination\InformationCollectionCollectionListAdapter;
use Netgen\InformationCollection\Core\Pagination\InformationCollectionCollectionListSearchAdapter;
use Netgen\InformationCollection\Core\Pagination\InformationCollectionContentsAdapter;
use Netgen\InformationCollection\Doctrine\Repository\EzInfoCollectionAttributeRepository;
use Netgen\InformationCollection\Doctrine\Repository\EzInfoCollectionRepository;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

use function array_merge;
use function count;
use function time;

class AdminController extends Controller
{
    protected InformationCollection $service;

    protected ContentService $contentService;

    protected ConfigResolverInterface $configResolver;

    protected Anonymizer $anonymizer;

    protected FieldDataFactory $factory;
    protected EzInfoCollectionRepository $infoCollectionRepository;

    protected EzInfoCollectionAttributeRepository $infoCollectionAttributeRepository;

    protected FormBuilder $builder;

    private TranslatorInterface $translator;

    public function __construct(
        InformationCollection $service,
        Anonymizer $anonymizer,
        ContentService $contentService,
        ConfigResolverInterface $configResolver,
        TranslatorInterface $translator,
        FieldDataFactory $factory,
        EzInfoCollectionRepository $infoCollectionRepository,
        EzInfoCollectionAttributeRepository $infoCollectionAttributeRepository,
        FormBuilder $builder
    ) {
        $this->service = $service;
        $this->contentService = $contentService;
        $this->configResolver = $configResolver;
        $this->anonymizer = $anonymizer;
        $this->translator = $translator;
        $this->factory = $factory;
        $this->infoCollectionRepository = $infoCollectionRepository;
        $this->infoCollectionAttributeRepository = $infoCollectionAttributeRepository;
        $this->builder = $builder;
    }

    /**
     * Displays overview page.
     */
    public function overviewAction(Request $request): Response
    {
        $this->checkReadPermissions();

        $adapter = new InformationCollectionContentsAdapter($this->service, Query::countQuery());
        $pager = $this->getPager($adapter, (int) $request->query->get('page'));

        return $this->render('@NetgenInformationCollection/admin/overview.html.twig', ['objects' => $pager]);
    }

    /**
     * Displays list of collection for selected Content.
     */
    public function collectionListAction(Request $request, Content $content): Response
    {
        $this->checkReadPermissions();

        $adapter = new InformationCollectionCollectionListAdapter($this->service, ContentId::withContentId($content->id));
        $pager = $this->getPager($adapter, (int) $request->query->get('page'));

        return $this->render('@NetgenInformationCollection/admin/collection_list.html.twig', [
            'objects' => $pager,
            'content' => $content,
        ]);
    }

    /**
     * Handles collection search.
     */
    public function searchAction(Request $request, Content $content): Response
    {
        $this->checkReadPermissions();

        $query = SearchQuery::withContentAndSearchText($content->id, $request->query->get('searchText'));

        $adapter = new InformationCollectionCollectionListSearchAdapter($this->service, $query);
        $pager = $this->getPager($adapter, (int) $request->query->get('page'));

        return $this->render(
            '@NetgenInformationCollection/admin/collection_list.html.twig',
            [
                'objects' => $pager,
                'content' => $content,
            ]
        );
    }

    /**
     * Displays individual collection details.
     */
    public function viewAction(Collection $collection): Response
    {
        $this->checkReadPermissions();

        return $this->render('@NetgenInformationCollection/admin/view.html.twig', [
            'collection' => $collection,
            'content' => $collection->getContent(),
        ]);
    }

    /**
     * Handles actions performed on overview page.
     */
    public function handleContentsAction(Request $request): RedirectResponse
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
     * Handles actions performed on collection list page.
     */
    public function handleCollectionListAction(Request $request): RedirectResponse
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
     * Handles action on collection details page.
     */
    public function handleCollectionAction(Request $request): RedirectResponse
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

            $this->addFlashMessage('success', 'collection_removed');

            return $this->redirectToRoute('netgen_information_collection.route.admin.collection_list', ['contentId' => $contentId]);
        }

        if ($request->request->has('AnonymizeCollectionAction')) {
            $this->checkAnonymizePermissions();

            $this->anonymizer->anonymizeCollection($collectionId);

            $this->addFlashMessage('success', 'collection_anonymized');

            return $this->redirectToRoute('netgen_information_collection.route.admin.view', ['collectionId' => $collectionId]);
        }

        $this->addFlashMessage('error', 'something_went_wrong');

        return $this->redirectToRoute('netgen_information_collection.route.admin.view', ['collectionId' => $collectionId]);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function editAction(Request $request, int $collectionId): RedirectResponse|Response
    {
        $this->checkEditPermissions();

        $collection = $this->service->getCollection(new CollectionId($collectionId));

        $location = $collection->getContent()->contentInfo->getMainLocation();

        if ($location === null) {
            $this->createNotFoundException();
        }

        $form = $this->builder->createUpdateFormForLocation($location, $collection)->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var InformationCollectionStruct $struct */
            $struct = $form->getData()->payload;

            /** @var ContentType $contentType */
            $contentType = $form->getData()->definition;

            $ezInfoCollection = $this->infoCollectionRepository->find($collectionId);

            if ($ezInfoCollection === null) {
                $this->createNotFoundException();
            }

            $ezInfoCollection->setModified(time());

            $this->infoCollectionRepository->save($ezInfoCollection);

            foreach ($struct->getCollectedFields() as $fieldDefIdentifier => $value) {
                if ($value === null) {
                    continue;
                }

                $fieldDefinition = $contentType->getFieldDefinition($fieldDefIdentifier);

                if ($fieldDefinition === null) {
                    continue;
                }

                $legacyValue = $this->factory->getLegacyValue($value, $fieldDefinition);

                $ezInfoAttribute = $this->infoCollectionAttributeRepository->findByCollectionIdAndFieldDefinitionId(
                    $collectionId,
                    $fieldDefinition->id
                );

                if ($ezInfoAttribute === null) {
                    $ezInfoAttribute = $this->infoCollectionAttributeRepository->getInstance();
                    $ezInfoAttribute->setContentObjectId($collection->getContent()->id);
                    $ezInfoAttribute->setContentObjectAttributeId($collection->getContent()->getField($fieldDefinition->identifier)->id);
                    $ezInfoAttribute->setContentClassAttributeId($fieldDefinition->id);
                    $ezInfoAttribute->setInformationCollectionId($collection->getId());
                }

                $ezInfoAttribute->setDataInt($legacyValue->getDataInt());
                $ezInfoAttribute->setDataFloat($legacyValue->getDataFloat());
                $ezInfoAttribute->setDataText($legacyValue->getDataText());

                $this->infoCollectionAttributeRepository->save($ezInfoAttribute);
            }

            return $this->redirectToRoute(
                'netgen_information_collection.route.admin.view',
                [
                    'contentId' => $location->contentInfo->id,
                    'collectionId' => $collection->getId(),
                ]
            );
        }

        return $this->render('@NetgenInformationCollection/admin/edit.html.twig', [
            'collection' => $collection,
            'content' => $location->getContent(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * Adds a flash message with specified parameters.
     */
    protected function addFlashMessage(string $messageType, string $message, int $count = 1, array $parameters = []): void
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
     * Returns configured instance of Pagerfanta.
     */
    protected function getPager(AdapterInterface $adapter, int $currentPage): Pagerfanta
    {
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

    protected function checkEditPermissions(): void
    {
        $attribute = new Attribute('infocollector', 'edit');
        $this->denyAccessUnlessGranted($attribute);
    }
}
