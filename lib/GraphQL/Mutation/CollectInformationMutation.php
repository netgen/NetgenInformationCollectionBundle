<?php

namespace Netgen\InformationCollection\GraphQL\Mutation;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use EzSystems\EzPlatformGraphQL\Schema\Domain\Content\NameHelper;
use Netgen\Bundle\InformationCollectionBundle\EzPlatform\RepositoryForms\InformationCollectionMapper;
use Netgen\Bundle\InformationCollectionBundle\EzPlatform\RepositoryForms\InformationCollectionType;
use Netgen\InformationCollection\Handler;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;

class CollectInformationMutation implements AliasedInterface, MutationInterface
{
    private LocationService $locationService;

    private Handler $informationCollectionHandler;

    private FormFactoryInterface $formFactory;

    private NameHelper $nameHelper;

    /**
     * @param LocationService $locationService
     * @param Handler $informationCollectionHandler
     */
    public function __construct(
        LocationService      $locationService,
        Handler              $informationCollectionHandler,
        FormFactoryInterface $formFactory,
        NameHelper           $nameHelper
    )
    {
        $this->locationService = $locationService;
        $this->informationCollectionHandler = $informationCollectionHandler;
        $this->formFactory = $formFactory;
        $this->nameHelper = $nameHelper;
    }

    public function performInformationCollection(int $locationId, array $input)
    {
        $location = $this->locationService->loadLocation($locationId);

        $content = $location->getContent();
        $contentType = $content->getContentType();
        $informationCollectionMapper = new InformationCollectionMapper();

        $data = $informationCollectionMapper->mapToFormData($content, $location, $contentType);

        $form = $this->formFactory->create(
            InformationCollectionType::class,
            $data,
            [
                'languageCode' => $content->contentInfo->mainLanguageCode,
                'mainLanguageCode' => $content->contentInfo->mainLanguageCode,
                'csrf_protection' => false
            ]
        );

        $transformedInput = [];
        /** @var FieldDefinition $fieldDefinition */
        foreach ($contentType->getFieldDefinitions() as $fieldDefinition) {
            $graphQlFieldName = $this->nameHelper->fieldDefinitionField($fieldDefinition);
            $inputElement = $input[$graphQlFieldName] ?? null;
            if (!$inputElement) {
                continue;
            }

            $transformedInput[$fieldDefinition->identifier] = [
                'value' => $fieldDefinition->fieldTypeIdentifier === 'ezselection' ? implode("-", (array)$inputElement) : $inputElement
            ];
        }

        $form->submit($transformedInput);
        if ($form->isSubmitted() && $form->isValid()) {
            $collectedData = $form->getData();
            $this->informationCollectionHandler->handle($collectedData, []);
            return [
                'success' => true
            ];
        }

        return [
            'success' => false,
            'errors' => array_map(
                static function (FormError $formError) {
                    return [
                        'message' => $formError->getMessage(),
                        'fieldIdentifier' => $formError->getOrigin() ? $formError->getOrigin()->getParent()->getName() : "unknown"
                    ];
                },
                iterator_to_array($form->getErrors(true))
            )
        ];
    }


    public static function getAliases(): array
    {
        return [
            'performInformationCollection' => 'CollectInformation'
        ];
    }
}