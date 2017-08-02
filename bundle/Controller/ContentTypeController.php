<?php

namespace Netgen\Bundle\InformationCollectionBundle\Controller;

use EzSystems\PlatformUIBundle\Controller\ContentTypeController as BaseContentTypeController;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
use EzSystems\RepositoryForms\Data\Mapper\ContentTypeDraftMapper;
use EzSystems\RepositoryForms\FieldType\FieldTypeFormMapperRegistryInterface;
use EzSystems\RepositoryForms\Form\ActionDispatcher\ActionDispatcherInterface;
use EzSystems\RepositoryForms\Form\Type\ContentType\ContentTypeDeleteType;
use Symfony\Component\HttpFoundation\Request;
use EzSystems\RepositoryForms\Form\Type\ContentType\ContentTypeUpdateType;

class ContentTypeController extends BaseContentTypeController
{
    /**
     * @var ContentTypeService
     */
    protected $contentTypeService;
    
    /**
     * @var SearchService
     */
    protected $searchService;
    
    /**
     * @var UserService
     */
    protected $userService;
    
    /**
     * @var ActionDispatcherInterface
     */
    protected $contentTypeActionDispatcher;
    
    /**
     * @var ActionDispatcherInterface
     */
    protected $contentTypeGroupActionDispatcher;
    
    /**
     * @var FieldTypeFormMapperRegistryInterface
     */
    protected $fieldTypeMapperRegistry;
    
    /**
     * @var string[]
     */
    protected $prioritizedLanguages = [];
    
    public function __construct(
        ContentTypeService $contentTypeService,
        SearchService $searchService,
        UserService $userService,
        ActionDispatcherInterface $contentTypeGroupActionDispatcher,
        ActionDispatcherInterface $contentTypeActionDispatcher,
        FieldTypeFormMapperRegistryInterface $fieldTypeMapperRegistry
        ) {
            $this->contentTypeService = $contentTypeService;
            $this->searchService = $searchService;
            $this->userService = $userService;
            $this->contentTypeGroupActionDispatcher = $contentTypeGroupActionDispatcher;
            $this->contentTypeActionDispatcher = $contentTypeActionDispatcher;
            $this->fieldTypeMapperRegistry = $fieldTypeMapperRegistry;
    }
    
    protected function getPrioritizedLanguage(ContentType $contentType)
    {
        foreach ($this->prioritizedLanguages as $prioritizedLanguage) {
            if (isset($contentType->names[$prioritizedLanguage])) {
                return $prioritizedLanguage;
            }
        }
        
        return $contentType->mainLanguageCode;
    }
    
    public function viewContentTypeAction($contentTypeId, $languageCode = null)
    {
        $contentType = $this->contentTypeService->loadContentType($contentTypeId);
        $countQuery = new Query([
            'filter' => new Query\Criterion\ContentTypeId($contentTypeId),
            'limit' => 0,
        ]);
        $contentCount = $this->searchService->findContent($countQuery, [], false)->totalCount;
        $deleteForm = $this->createForm(ContentTypeDeleteType::class, ['contentTypeId' => $contentTypeId]);
        
        if (!isset($languageCode) || !isset($contentType->names[$languageCode])) {
            $languageCode = $this->getPrioritizedLanguage($contentType);
        }
        
        $fieldDefinitionsByGroup = [];
        foreach ($contentType->fieldDefinitions as $fieldDefinition) {
            $fieldDefinitionsByGroup[$fieldDefinition->fieldGroup ?: 'content'][] = $fieldDefinition;
        }
        
        return $this->render('NetgenInformationCollectionBundle:ContentType:view.html.twig', [
            'language_code' => $languageCode,
            'content_type' => $contentType,
            'content_count' => $contentCount,
            'modifier' => $this->userService->loadUser($contentType->modifierId),
            'delete_form' => $deleteForm->createView(),
            'fielddefinitions_by_group' => $fieldDefinitionsByGroup,
            'can_edit' => $this->isGranted(new Attribute('class', 'update')),
            'can_delete' => ($this->isGranted(new Attribute('class', 'delete')) && !$this->contentTypeService->isContentTypeUsed($contentType)),
        ]);
    }
    
    public function updateContentTypeAction(Request $request, $contentTypeId, $languageCode = null)
    {
        // First try to load the draft.
        // If it doesn't exist, create it.
        try {
            $contentTypeDraft = $this->contentTypeService->loadContentTypeDraft($contentTypeId);
        } catch (NotFoundException $e) {
            $contentTypeDraft = $this->contentTypeService->createContentTypeDraft(
                $this->contentTypeService->loadContentType($contentTypeId)
                );
        }
        
        if (!isset($languageCode) || !isset($contentTypeDraft->names[$languageCode])) {
            $languageCode = $this->getPrioritizedLanguage($contentTypeDraft);
        }
        
        $contentTypeData = (new ContentTypeDraftMapper())->mapToFormData($contentTypeDraft);
        $form = $this->createForm(
            ContentTypeUpdateType::class,
            $contentTypeData,
            ['languageCode' => $languageCode]
            );
        $actionUrl = $this->generateUrl(
            'admin_contenttypeUpdate',
            ['contentTypeId' => $contentTypeId, 'languageCode' => $languageCode]
            );
        
        // Synchronize form and data.
        $form->handleRequest($request);
        $hasErrors = false;
        if ($form->isValid()) {
            $this->contentTypeActionDispatcher->dispatchFormAction(
                $form,
                $contentTypeData,
                $form->getClickedButton() ? $form->getClickedButton()->getName() : null,
                ['languageCode' => $languageCode]
                );
            
            if ($response = $this->contentTypeActionDispatcher->getResponse()) {
                return $response;
            }
            
            return $this->redirectAfterFormPost($actionUrl);
        } elseif ($form->isSubmitted()) {
            $hasErrors = true;
        }
        
        return $this->render('NetgenInformationCollectionBundle:ContentType:update_content_type.html.twig', [
            'form' => $form->createView(),
            'action_url' => $actionUrl,
            'contentTypeName' => $contentTypeDraft->getName($languageCode),
            'contentTypeDraft' => $contentTypeDraft,
            'modifier' => $this->userService->loadUser($contentTypeDraft->modifierId),
            'languageCode' => $languageCode,
            'hasErrors' => $hasErrors,
        ]);
    }
}