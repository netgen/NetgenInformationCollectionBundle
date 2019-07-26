<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ExportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('dateFrom', DateType::class, [
            'required' => true,
            'widget' => 'single_text',
            'label' => 'netgen_information_collection_admin_export_from',
            'translation_domain' => 'netgen_information_collection_admin',
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Date(),
            ],
        ]);

        $builder->add('dateTo', DateType::class, [
            'required' => true,
            'widget' => 'single_text',
            'label' => 'netgen_information_collection_admin_export_to',
            'translation_domain' => 'netgen_information_collection_admin',
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Date(),
            ],
        ]);

        $builder->add('offset', IntegerType::class, [
            'required' => true,
            'data' => 0,
            'label' => 'netgen_information_collection_admin_offset',
            'translation_domain' => 'netgen_information_collection_admin',
            'constraints' => [
                new Assert\NotBlank(),
            ],
        ]);

        $builder->add('limit', IntegerType::class, [
            'required' => true,
            'data' => 100,
            'label' => 'netgen_information_collection_admin_limit',
            'translation_domain' => 'netgen_information_collection_admin',
            'constraints' => [
                new Assert\NotBlank(),
            ],
        ]);

        $builder->add('export', SubmitType::class, [
            'label' => 'netgen_information_collection_admin_export_export',
            'translation_domain' => 'netgen_information_collection_admin',
        ]);

        $builder->add('cancel', SubmitType::class, [
            'label' => 'netgen_information_collection_admin_export_cancel',
            'translation_domain' => 'netgen_information_collection_admin',
        ]);
    }
}
