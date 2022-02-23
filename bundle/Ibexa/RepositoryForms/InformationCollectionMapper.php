<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Ibexa\RepositoryForms;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Netgen\InformationCollection\API\Value\InformationCollectionStruct;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Ibexa\ContentForms\Data\Mapper\FormDataMapperInterface;
use Ibexa\Contracts\ContentForms\Data\Content\FieldData;

final class InformationCollectionMapper
{
    /**
     * Maps a ValueObject from Ibexa content repository to a data usable as underlying form data (e.g. create/update struct).
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $contentDraft
     * @param array $params
     *
     * @return InformationCollectionStruct
     */
    public function mapToFormData(Content $content, Location $location, ContentType $contentType)
    {
        $fields = $content->getFieldsByLanguage($content->contentInfo->mainLanguageCode);

        $informationCollectionFields = [];

        /** @var FieldDefinition $fieldDef */
        foreach ($contentType->fieldDefinitions as $fieldDef) {
            if ($fieldDef->isInfoCollector) {
                $field = $fields[$fieldDef->identifier];

                $fieldData = new FieldData(
                    [
                        'fieldDefinition' => $fieldDef,
                        'field' => $field,
                    ]
                );

                $informationCollectionFields[] = $fieldData;
            }
        }

        return new InformationCollectionStruct(
            $content,
            $location,
            $contentType,
            $informationCollectionFields
        );
    }
}
