<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Form\FieldTypeHandler;

use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\RelationList\Value as RelationListValue;
use Netgen\Bundle\InformationCollectionBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

final class RelationList extends FieldTypeHandler
{
    public const BROWSE = 0;
    public const DROPDOWN = 1;
    public const LIST_RADIO = 2;
    public const LIST_CHECK = 3;
    public const MULTIPLE_SELECTION = 4;
    public const TPLBASED_MULTI = 5;
    public const TPLBASED_SINGLE = 6;

    private Repository $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function convertFieldValueToForm(Value $value, ?FieldDefinition $fieldDefinition = null): ?array
    {
        if (empty($value->destinationContentIds)) {
            return null;
        }

        return $value->destinationContentIds;
    }

    public function convertFieldValueFromForm($data): RelationListValue
    {
        return new RelationListValue($data);
    }

    protected function buildFieldForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        string $languageCode,
        ?Content $content = null
    ): void {
        $options = $this->getDefaultFieldOptions($fieldDefinition, $languageCode, $content);

        $fieldSettings = $fieldDefinition->getFieldSettings();

        $selectionMethod = $fieldSettings['selectionMethod'];

        $defaultLocation = $fieldSettings['selectionDefaultLocation'];
        $contentTypes = $fieldSettings['selectionContentTypes'];

        /* TODO: implement different selection methods */
        switch ($fieldSettings['selectionMethod']) {
            case self::MULTIPLE_SELECTION:
                $locationService = $this->repository->getLocationService();
                $location = $locationService->loadLocation($defaultLocation ?: 2);
                $locationList = $locationService->loadLocationChildren($location);

                $choices = [];

                /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $child */
                foreach ($locationList->locations as $child) {
                    $choices[$child->getContent()->getName()] = $child->contentInfo->id;
                }

                $formBuilder->add($fieldDefinition->identifier, ChoiceType::class, [
                    'choices' => $choices,
                    'expanded' => false,
                    'multiple' => true,
                ] + $options);

                break;

            default:
                $locationService = $this->repository->getLocationService();
                $location = $locationService->loadLocation($defaultLocation ?: 2);
                $locationList = $locationService->loadLocationChildren($location);

                $choices = [];

                /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $child */
                foreach ($locationList->locations as $child) {
                    $choices[$child->getContent()->getName()] = $child->contentInfo->id;
                }

                $formBuilder->add($fieldDefinition->identifier, ChoiceType::class, [
                    'choices' => $choices,
                    'expanded' => false,
                    'multiple' => false,
                ] + $options);

                break;
        }
    }
}
