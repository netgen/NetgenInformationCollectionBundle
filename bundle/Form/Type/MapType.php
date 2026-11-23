<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class MapType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('address', TextType::class, [
            'label' => 'ibexa_forms.form.map.address.label',
        ]);

        $builder->add('latitude', NumberType::class, [
            'label' => 'ibexa_forms.form.map.latitude.label',
        ]);

        $builder->add('longitude', NumberType::class, [
            'label' => 'ibexa_forms.form.map.longitude.label',
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'ibexa_forms_map';
    }
}
