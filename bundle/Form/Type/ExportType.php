<?php

namespace Netgen\Bundle\InformationCollectionBundle\Form\Type;

use eZ\Publish\API\Repository\Repository;
use Netgen\Bundle\InformationCollectionBundle\API\Value\Export\ExportCriteria;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ExportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('dateFrom', DateType::class, [
            'required' => false,
            'widget' => 'single_text',
            'label' => 'netgen_information_collection_admin_export_from',
            'translation_domain' => 'netgen_information_collection_admin',
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Date(),
            ],
        ]);

        $builder->add('dateTo', DateType::class, [
            'required' => false,
            'widget' => 'single_text',
            'label' => 'netgen_information_collection_admin_export_to',
            'translation_domain' => 'netgen_information_collection_admin',
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Date(),
            ],
        ]);

        $builder->add('exportType', ChoiceType::class, [
            'required' => true,
            'choices' => [
                'netgen_information_collection_admin_export_type_csv' => 'csv',
                'netgen_information_collection_admin_export_type_xls' => 'xls',
            ],
            'translation_domain' => 'netgen_information_collection_admin',
            'label' => 'netgen_information_collection_admin_export_type',
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
