<?php

namespace Netgen\Bundle\InformationCollectionBundle\Form\Type;

use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use Netgen\Bundle\EzFormsBundle\Form\Payload\InformationCollectionStruct;
use Netgen\Bundle\InformationCollectionBundle\API\Value\InformationCollection\Collection;
use RuntimeException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Netgen\Bundle\EzFormsBundle\Form\Type\AbstractContentType;

/**
 * Class InformationCollectionType.
 */
class InformationCollectionUpdateType extends AbstractContentType
{
    /**
     * @var array
     */
    protected $languages;

    /**
     * Sets system available array of languages.
     *
     * @param array $languages
     */
    public function setLanguages(array $languages)
    {
        $this->languages = $languages;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix()
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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var DataWrapper $dataWrapper */
        $dataWrapper = $options['data'];
        $collection = $options['collection'];

        if (!$dataWrapper instanceof DataWrapper) {
            throw new RuntimeException(
                'Data must be an instance of Netgen\\EzFormsBundle\\Form\\DataWrapper'
            );
        }

        /** @var InformationCollectionStruct $payload */
        $payload = $dataWrapper->payload;

        if (!$payload instanceof InformationCollectionStruct) {
            throw new RuntimeException(
                'Data payload must be an instance of Netgen\\Bundle\\EzFormsBundle\\Form\\Payload\\InformationCollectionStruct'
            );
        }

        /** @var ContentType $contentType */
        $contentType = $dataWrapper->definition;

        if (!$contentType instanceof ContentType) {
            throw new RuntimeException(
                'Data definition must be an instance of eZ\\Publish\\API\\Repository\\Values\\ContentType\\ContentType'
            );
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

            $handler->buildFieldUpdateForm($builder, $fieldDefinition, $dataWrapper->target->content, $this->getLanguageCode($contentType));
        }
    }

    /**
     * If ContentType language code is in languages array then use it, else use first available one.
     *
     * @param ContentType $contentType
     *
     * @return string
     */
    protected function getLanguageCode(ContentType $contentType)
    {
        $contentTypeLanguages = array_keys($contentType->getNames());

        foreach ($this->languages as $languageCode) {
            if (in_array($languageCode, $contentTypeLanguages, true)) {
                return $languageCode;
            }
        }

        return $contentType->mainLanguageCode;
    }
}
