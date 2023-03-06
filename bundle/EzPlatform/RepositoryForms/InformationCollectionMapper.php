<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\EzPlatform\RepositoryForms;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\API\Repository\Values\ValueObject;
use Netgen\InformationCollection\API\Value\InformationCollectionStruct;
use Symfony\Component\OptionsResolver\OptionsResolver;
use EzSystems\EzPlatformContentForms\Data\Mapper\FormDataMapperInterface;
use EzSystems\EzPlatformContentForms\Data\Content\FieldData;

final class InformationCollectionMapper
{
    /**
     * Maps a ValueObject from eZ content repository to a data usable as underlying form data (e.g. create/update struct).
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Content $contentDraft
     * @param array $params
     *
     * @return InformationCollectionStruct
     */
    public function mapToFormData(Content $content, Location $location, ContentType $contentType)
    {
        $fields = $content->getFieldsByLanguage();

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
