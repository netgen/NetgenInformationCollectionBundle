<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Form\Type\FieldType;

use Ibexa\ContentForms\Form\Type\FieldType\CountryFieldType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class CountryFieldTypePlaceholderExtension extends AbstractTypeExtension
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public static function getExtendedTypes(): iterable
    {
        return [CountryFieldType::class];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'placeholder' => $this->translator->trans('form.field_type.ezcountry.placeholder', [], 'ezplatform_content_forms_content'),
            ]
        );
    }
}
