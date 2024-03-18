<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Form\Type\FieldType;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

final class UserUpdateType extends UserType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $emailOptions = [
            'label' => 'E-mail address',
            'constraints' => [
                new Constraints\NotBlank(),
                new Constraints\Email(),
            ],
        ];

        $passwordOptions = [
            'type' => PasswordType::class,
            // Setting required to false enables passing empty passwords for no update
            'required' => false,
            'invalid_message' => 'Both passwords must match.',
            'options' => [
                'constraints' => $this->getPasswordConstraints($options['ibexa_forms']['fielddefinition'] ?? null, false),
            ],
            'first_options' => [
                'label' => 'New password (leave empty to keep current password)',
            ],
            'second_options' => [
                'label' => 'Repeat new password',
            ],
        ];

        $builder
            ->add('email', EmailType::class, $emailOptions)
            ->add('password', RepeatedType::class, $passwordOptions);
    }

    public function getBlockPrefix(): string
    {
        return 'ibexa_forms_ezuser_update';
    }
}
