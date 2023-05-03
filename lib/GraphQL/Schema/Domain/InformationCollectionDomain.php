<?php

namespace Netgen\InformationCollection\GraphQL\Schema\Domain;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use EzSystems\EzPlatformGraphQL\Schema;
use EzSystems\EzPlatformGraphQL\Schema\Builder;
use EzSystems\EzPlatformGraphQL\Schema\Domain;
use Generator;

class InformationCollectionDomain implements Domain\Iterator, Schema\Worker
{
    const ARG = 'InformationCollectionTypes';

    private ContentTypeService $contentTypeService;

    private Domain\Content\NameHelper $nameHelper;

    private array $typesMap;

    /**
     * @param ContentTypeService $contentTypeService
     */
    public function __construct(ContentTypeService $contentTypeService, Domain\Content\NameHelper $nameHelper, array $typesMap)
    {
        $this->contentTypeService = $contentTypeService;
        $this->nameHelper = $nameHelper;
        $this->typesMap = $typesMap;
    }

    public function init(Builder $schema)
    {
        $contentTypes = $this->getCollectingContentTypes();
        if ($contentTypes === null) {
            return;
        }

        $schema->addType(
            new Builder\Input\Type(
                'InformationCollectionContentTypes',
                'enum'
            )
        );

        foreach ($contentTypes as $contentType) {
            $schema->addValueToEnum(
                'InformationCollectionContentTypes',
                new Builder\Input\EnumValue($this->nameHelper->domainContentTypeName($contentType))
            );
        }

        $this->addInformationCollectionResultTypes($schema);
    }

    public function iterate(): Generator
    {
        $types = $this->getCollectingContentTypes();
        foreach ($types as $type) {
            yield [self::ARG => $type];
        }
    }

    public function work(Builder $schema, array $args)
    {
        /** @var ContentType $contentType */
        $contentType = $args[self::ARG];
        $fieldDefinitions = $this->getCollectingFieldDefinitions($contentType);

        $collectionContentTypeName = "Collect" . $this->nameHelper->domainContentName($contentType);

        $schema->addType(new Builder\Input\Type(
            $collectionContentTypeName,
            'input-object'
        ));

        array_walk($fieldDefinitions, function (FieldDefinition $fieldDefinition) use ($schema, $collectionContentTypeName, $contentType) {
            $schema->addFieldToType(
                $collectionContentTypeName,
                new Builder\Input\Field(
                    $this->nameHelper->fieldDefinitionField($fieldDefinition),
                    $this->typesMap[$fieldDefinition->fieldTypeIdentifier]['input_type'] ?? $this->typesMap[$fieldDefinition->fieldTypeIdentifier]['value_type']
                )
            );
        });

        $collectionMutationFieldName = str_replace("create", "collect", $this->nameHelper->domainMutationCreateContentField($contentType));

        // add the specific mutation
        $schema->addFieldToType(
            'DomainContentMutation',
            new Builder\Input\Field(
                $collectionMutationFieldName,
                'InformationCollectionResult', // update: Return value of collectInformation
                [
                    'resolve' => '@=mutation("CollectInformation", [args["locationId"], args["input"]])'
                ]
            )
        );

        $schema->addArgToField(
            'DomainContentMutation',
            $collectionMutationFieldName,
            new Builder\Input\Arg(
                'input',
                $collectionContentTypeName
            )
        );

        $schema->addArgToField(
            'DomainContentMutation',
            $collectionMutationFieldName,
            new Builder\Input\Arg(
                'locationId',
                "Int!"
            )
        );
    }

    private function addInformationCollectionResultTypes(Builder $schema)
    {
        // InformationCollectionError
        $schema->addType(
            new Builder\Input\Type(
                'InformationCollectionError',
                'object'
            )
        );

        $schema->addFieldToType(
            'InformationCollectionError',
            new Builder\Input\Field(
                'message',
                'String!'
            )
        );

        $schema->addFieldToType(
            'InformationCollectionError',
            new Builder\Input\Field(
                'fieldIdentifier',
                'String!'
            )
        );

        // InformationCollectionResult
        $schema->addType(
            new Builder\Input\Type(
                'InformationCollectionResult',
                'object'
            )
        );

        $schema->addFieldToType(
            'InformationCollectionResult',
            new Builder\Input\Field(
                'errors',
                '[InformationCollectionError]'
            )
        );

        $schema->addFieldToType(
            'InformationCollectionResult',
            new Builder\Input\Field(
                'success',
                'Boolean!'
            )
        );

    }

    public function canWork(Builder $schema, array $args)
    {
        return array_key_exists(self::ARG, $args);
    }

    private function getCollectingContentTypes(): Generator
    {
        foreach ($this->contentTypeService->loadContentTypeGroups() as $group) {
            foreach ($this->contentTypeService->loadContentTypes($group) as $type) {
                if ($this->contentTypeCollectsInformation($type)) {
                    yield $type;
                }
            }
        }
    }

    private function contentTypeCollectsInformation(ContentType $contentType): bool
    {
        return !empty($this->getCollectingFieldDefinitions($contentType));
    }

    /**
     * @param ContentType $contentType
     * @return FieldDefinition[]
     */
    private function getCollectingFieldDefinitions(ContentType $contentType): array
    {
        $definitions = $contentType->getFieldDefinitions();
        return array_filter($definitions->toArray(), function (FieldDefinition $fieldDefinition) {
            return $fieldDefinition->isInfoCollector;
        });
    }
}