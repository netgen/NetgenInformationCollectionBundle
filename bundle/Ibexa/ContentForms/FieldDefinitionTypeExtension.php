<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Ibexa\ContentForms;

use Ibexa\AdminUi\Form\Type\FieldDefinition\FieldDefinitionType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class FieldDefinitionTypeExtension extends AbstractTypeExtension
{
    public function getExtendedType(): string
    {
        return FieldDefinitionType::class;
    }

    public static function getExtendedTypes(): iterable
    {
        return [FieldDefinitionType::class];
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isTranslation = $options['languageCode'] !== $options['mainLanguageCode'];

        $builder->add('isInfoCollector', CheckboxType::class, [
            'required' => false,
            'label' => 'field_definition.is_infocollector',
            'disabled' => $isTranslation,
        ]);
    }
}
