<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Form\Builder;

use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\Repository\SiteAccessAware\ContentTypeService;
use Netgen\Bundle\IbexaFormsBundle\Form\DataWrapper;
use Netgen\Bundle\IbexaFormsBundle\Form\Payload\InformationCollectionStruct;
use Netgen\Bundle\InformationCollectionBundle\Form\InformationCollectionUpdateType;
use Netgen\InformationCollection\API\FieldHandler\CustomLegacyFieldHandlerInterface;
use Netgen\InformationCollection\API\Value\Attribute;
use Netgen\InformationCollection\API\Value\Collection;
use Netgen\InformationCollection\API\Value\Legacy\FieldValue;
use Netgen\InformationCollection\Core\Factory\FieldDataFactory;
use Netgen\InformationCollection\Core\Persistence\FieldHandler\FieldHandlerRegistry;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\RouterInterface;

class FormBuilder
{
    public function __construct(
        protected readonly FormFactoryInterface $formFactory,
        protected readonly ContentTypeService $contentTypeService,
        protected readonly RouterInterface $router,
        protected readonly ConfigResolverInterface $configResolver,
        protected readonly FieldDataFactory $legacyFactory,
        protected readonly FieldHandlerRegistry $registry
    ) {
    }

    public function createUpdateFormForLocation(Location $location, Collection $collection): FormBuilderInterface
    {
        $contentInfo = $location->contentInfo;
        $contentType = $this->contentTypeService->loadContentType($contentInfo->contentTypeId);
        $struct = new InformationCollectionStruct();

        foreach ($collection->getAttributes() as $attribute) {
            $fieldValue = $this->fromAttribute($attribute);

            if ($fieldValue !== null) {
                $struct->setCollectedFieldValue($attribute->getField()->getFieldDefinitionIdentifier(), $fieldValue);
            }
        }

        $data = new DataWrapper($struct, $contentType, $location);

        $useCsrf = $this->configResolver->getParameter('information_collection.form.use_csrf', 'netgen');

        return $this->formFactory
            ->createNamedBuilder(
                sprintf('%s_%d', $contentType->identifier, $location->id),
                InformationCollectionUpdateType::class,
                $data,
                [
                    'collection' => $collection,
                    'csrf_protection' => $useCsrf,
                ]
            );
    }

    private function fromAttribute(Attribute $attribute)
    {
        $legacyData = new FieldValue(
            $attribute->getField()->id,
            $attribute->getValue()->getDataText(),
            $attribute->getValue()->getDataInt(),
            $attribute->getValue()->getDataFloat()
        );

        $handler = $this->registry->handle($attribute->getFieldDefinition()->defaultValue);
        if (!$handler instanceof CustomLegacyFieldHandlerInterface) {
            return null;
        }

        return $handler->fromLegacyValue($legacyData);
    }
}
