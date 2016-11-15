<?php

namespace Netgen\Bundle\InformationCollectionBundle\Form\Builder;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\RouterInterface;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\ContentTypeService;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use Netgen\Bundle\EzFormsBundle\Form\Payload\InformationCollectionStruct;

class FormBuilder
{
    const EZFORMS_INFORMATION_COLLECTION = 'ezforms_information_collection';

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var boolean
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
     * @param RouterInterface $router
     * @param bool $useCsrf
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        ContentTypeService $contentTypeService,
        RouterInterface $router,
        $useCsrf
    )
    {
        $this->formFactory = $formFactory;
        $this->useCsrf = $useCsrf;
        $this->contentTypeService = $contentTypeService;
        $this->router = $router;
    }

    /**
     * Creates Information collection Form object for given Location object
     *
     * @param Location $location
     * @param bool $useAjax
     *
     * @return FormBuilderInterface
     */
    public function createFormForLocation(Location $location, $useAjax = false)
    {
        $contentInfo = $location->contentInfo;

        $contentType = $this->contentTypeService->loadContentType( $contentInfo->contentTypeId );

        $data = new DataWrapper( new InformationCollectionStruct(), $contentType, $location );

        $formBuilder = $this->formFactory
            ->createBuilder(
                self::EZFORMS_INFORMATION_COLLECTION,
                $data,
                [
                    'csrf_protection' => $this->useCsrf,
                ]
            );

        if ($useAjax) {
            $formBuilder->setAction($this->router->generate('netgen_information_collection_handle_ajax', ['location' => $location->id]));
        }

        return $formBuilder;
    }
}