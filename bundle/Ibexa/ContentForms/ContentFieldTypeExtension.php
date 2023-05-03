<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Ibexa\ContentForms;

use Ibexa\ContentForms\Form\Type\Content\ContentFieldType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

class ContentFieldTypeExtension extends AbstractTypeExtension
{
    public function getExtendedType(): string
    {
        return ContentFieldType::class;
    }

    public static function getExtendedTypes(): iterable
    {
        return [ContentFieldType::class];
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $fieldIdentifier = $builder->getName();

        /** @var \Ibexa\ContentForms\Data\Content\ContentUpdateData $updateStruct */
        $updateStruct = $options['contentUpdateStruct'];

        if (null === $updateStruct) {
            return;
        }

        $fieldDefinition = $updateStruct->fieldsData[$fieldIdentifier]->fieldDefinition;
        if ($fieldDefinition->isInfoCollector) {
            $builder->setRequired(false);

            (function () {
                $this->isRequired = false;
            })->call($fieldDefinition);
        }
    }
}
