<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Form\Builder;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\Content\Content;
use Netgen\InformationCollection\API\Form\DynamicFormBuilderInterface;
use Netgen\InformationCollection\Integration\RepositoryForms\InformationCollectionMapper;
use Netgen\InformationCollection\Integration\RepositoryForms\InformationCollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\RouterInterface;

class FormBuilder implements DynamicFormBuilderInterface
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var bool
     */
    protected $useCsrf;

    /**
     * @var ContentTypeService
     */
    protected $contentTypeService;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * FormBuilder constructor.
     *
     * @param FormFactoryInterface $formFactory
     * @param ContentTypeService $contentTypeService
     */
    public function __construct(FormFactoryInterface $formFactory, ContentTypeService $contentTypeService)
    {
        $this->formFactory = $formFactory;
        $this->contentTypeService = $contentTypeService;
    }

    public function createFormWithAjax(Content $content): FormInterface
    {
        throw new \RuntimeException('This method is not implemented.');
    }

    public function createForm(Content $content): FormInterface
    {
        $contentType = $this->contentTypeService->loadContentType($content->contentInfo->contentTypeId);

        $informationCollectionMapper = new InformationCollectionMapper();

        $data = $informationCollectionMapper->mapToFormData($content, $contentType);

        return $this->formFactory->create(InformationCollectionType::class, $data, [
            'languageCode' => $content->contentInfo->mainLanguageCode,
            'mainLanguageCode' => $content->contentInfo->mainLanguageCode,
        ]);
    }
}
