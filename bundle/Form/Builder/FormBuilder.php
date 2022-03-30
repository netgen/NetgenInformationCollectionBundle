<?php

namespace Netgen\Bundle\InformationCollectionBundle\Form\Builder;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use Netgen\Bundle\EzFormsBundle\Form\Payload\InformationCollectionStruct;
use Netgen\Bundle\EzFormsBundle\Form\Type\InformationCollectionType;
use Netgen\Bundle\InformationCollectionBundle\Factory\LegacyDataFactoryInterface;
use Netgen\Bundle\InformationCollectionBundle\Form\Type\InformationCollectionUpdateType;
use Netgen\Bundle\InformationCollectionBundle\API\Value\InformationCollection\Collection;
use Netgen\Bundle\InformationCollectionBundle\Value\LegacyData;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouterInterface;

class FormBuilder
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var ConfigResolverInterface
     */
    protected $configResolver;

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
     * @param ConfigResolverInterface $configResolver
     * @param LegacyDataFactoryInterface $legacyFactory
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        ContentTypeService $contentTypeService,
        RouterInterface $router,
        ConfigResolverInterface $configResolver,
        LegacyDataFactoryInterface $legacyFactory
    ) {
        $this->formFactory = $formFactory;
        $this->configResolver = $configResolver;
        $this->contentTypeService = $contentTypeService;
        $this->router = $router;
        $this->legacyFactory = $legacyFactory;
    }

    /**
     * Creates Information collection Form object for given Location object.
     *
     * @param Location $location
     * @param bool $useAjax
     *
     * @return FormBuilderInterface
     */
    public function createFormForLocation(Location $location, $useAjax = false)
    {
        $contentInfo = $location->contentInfo;
        $contentType = $this->contentTypeService->loadContentType($contentInfo->contentTypeId);

        $data = new DataWrapper(new InformationCollectionStruct(), $contentType, $location);
        $useCsrf = $this->configResolver->getParameter('information_collection.form.use_csrf', 'netgen');

        $formBuilder = $this->formFactory
            ->createNamedBuilder(
                $contentType->identifier . '_' . $location->id,
                Kernel::VERSION_ID < 20800 ?
                    'ezforms_information_collection' :
                    InformationCollectionType::class,
                $data,
                array(
                    'csrf_protection' => $useCsrf,
                )
            );

        if ($useAjax) {
            $formBuilder->setAction($this->router->generate('netgen_information_collection_handle_ajax', array('location' => $location->id)));
        }

        return $formBuilder;
    }

    /**
     * Creates Information collection Form object for given Location object.
     *
     * @param Location $location
     * @param Collection $collection
     *
     * @return FormBuilderInterface
     */
    public function createUpdateFormForLocation(Location $location, Collection $collection)
    {
        $contentInfo = $location->contentInfo;
        $contentType = $this->contentTypeService->loadContentType($contentInfo->contentTypeId);
        $struct = new InformationCollectionStruct();

        foreach ($collection->attributes as $attribute) {
            $fieldValue = $this->legacyFactory->fromLegacyValue(
                new LegacyData(
                    $attribute->field->id,
                    $attribute->entity->getDataFloat(),
                    $attribute->entity->getDataInt(),
                    $attribute->entity->getDataText()
                ),
                $attribute->field
            );

            if ($fieldValue !== null) {
                $struct->setCollectedFieldValue($attribute->field->identifier, $fieldValue);
            }
        }

        $data = new DataWrapper($struct, $contentType, $location);
        $useCsrf = $this->configResolver->getParameter('information_collection.form.use_csrf', 'netgen');

        return $this->formFactory
            ->createNamedBuilder(
                $contentType->identifier . '_' . $location->id,
                Kernel::VERSION_ID < 20800 ?
                    'ezforms_information_collection_update' :
                    InformationCollectionUpdateType::class,
                $data,
                array(
                    'collection' => $collection,
                    'csrf_protection' => $useCsrf,
                )
            );
    }
}
