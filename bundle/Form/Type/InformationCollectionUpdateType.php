<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Form\Type;

use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Netgen\Bundle\InformationCollectionBundle\Form\DataWrapper;
use Netgen\Bundle\InformationCollectionBundle\Form\FieldTypeHandlerRegistry;
use Netgen\Bundle\InformationCollectionBundle\Form\Payload\InformationCollectionStruct;
use Netgen\InformationCollection\API\Value\Collection;
use RuntimeException;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function array_keys;
use function in_array;
use function sprintf;

class InformationCollectionUpdateType extends AbstractContentType
{
    protected FieldTypeHandlerRegistry $fieldTypeHandlerRegistry;

    protected DataMapperInterface $dataMapper;

    protected ConfigResolverInterface $configResolver;

    public function __construct(
        FieldTypeHandlerRegistry $fieldTypeHandlerRegistry,
        DataMapperInterface $dataMapper,
        ConfigResolverInterface $configResolver
    ) {
        parent::__construct($fieldTypeHandlerRegistry, $dataMapper);
        $this->configResolver = $configResolver;
    }

    public function getName(): string
    {
        return $this->getBlockPrefix();
    }

    /**
     * Returns the prefix of the template block name for this type.
     */
    public function getBlockPrefix(): string
    {
        return 'ezforms_information_collection_update';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('collection');
        $resolver->setAllowedTypes('collection', Collection::class);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var DataWrapper $dataWrapper */
        $dataWrapper = $options['data'];

        if (!$dataWrapper instanceof DataWrapper) {
            throw new RuntimeException(sprintf('Data must be an instance of %s', DataWrapper::class));
        }

        /** @var InformationCollectionStruct $payload */
        $payload = $dataWrapper->payload;

        if (!$payload instanceof InformationCollectionStruct) {
            throw new RuntimeException(sprintf('Data payload must be an instance of %s', InformationCollectionStruct::class));
        }

        /** @var ContentType $contentType */
        $contentType = $dataWrapper->definition;

        if (!$contentType instanceof ContentType) {
            throw new RuntimeException(sprintf('Data definition must be an instance of %s', ContentType::class));
        }

        $builder->setDataMapper($this->dataMapper);

        foreach ($contentType->getFieldDefinitions() as $fieldDefinition) {
            if ($fieldDefinition->fieldTypeIdentifier === 'ezuser') {
                continue;
            }

            if (!$fieldDefinition->isInfoCollector) {
                continue;
            }

            $handler = $this->fieldTypeHandlerRegistry->get($fieldDefinition->fieldTypeIdentifier);

            $handler->buildFieldUpdateForm(
                $builder,
                $fieldDefinition,
                $dataWrapper->target->content,
                $this->getLanguageCode($contentType)
            );
        }
    }

    /**
     * If ContentType language code is in languages array then use it, else use the main language.
     *
     * @param ContentType $contentType
     *
     * @return string
     */
    protected function getLanguageCode(ContentType $contentType): string
    {
        $contentTypeLanguages = array_keys($contentType->getNames());
        $languages = $this->configResolver->getParameter('languages');

        foreach ($languages as $languageCode) {
            if (in_array($languageCode, $contentTypeLanguages, true)) {
                return $languageCode;
            }
        }

        return $contentType->mainLanguageCode;
    }
}
