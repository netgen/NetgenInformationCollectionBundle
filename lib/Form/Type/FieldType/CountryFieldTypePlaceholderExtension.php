<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Form\Type\FieldType;

use Ibexa\ContentForms\Form\Type\FieldType\CountryFieldType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CountryFieldTypePlaceholderExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        return [CountryFieldType::class];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'placeholder' => 'form.field_type.ezcountry.placeholder',
            ]
        );
    }
}
