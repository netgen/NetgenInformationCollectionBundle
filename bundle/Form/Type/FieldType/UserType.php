<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Form\Type\FieldType;

use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints;

abstract class UserType extends AbstractType
{
    protected function getPasswordConstraints(?FieldDefinition $definition = null, bool $required = true): array
    {
        $passwordConstraints = [];

        if ($required) {
            $passwordConstraints[] = new Constraints\NotBlank();
        }

        if ($definition === null) {
            return $passwordConstraints;
        }

        $passwordValidator = $definition->validatorConfiguration['PasswordValueValidator'];

        if ($passwordValidator['requireAtLeastOneUpperCaseCharacter'] ?? false) {
            $passwordConstraints[] = new Constraints\Regex(
                [
                    'pattern' => '/[A-Z]+/',
                    'message' => 'Password must contain at least one upper-case character',
                ]
            );
        }

        if ($passwordValidator['requireAtLeastOneLowerCaseCharacter'] ?? false) {
            $passwordConstraints[] = new Constraints\Regex(
                [
                    'pattern' => '/[a-z]+/',
                    'message' => 'Password must contain at least one lower-case character',
                ]
            );
        }

        if ($passwordValidator['requireAtLeastOneNumericCharacter'] ?? false) {
            $passwordConstraints[] = new Constraints\Regex(
                [
                    'pattern' => '/\d+/',
                    'message' => 'Password must contain at least one numeric character',
                ]
            );
        }

        if ($passwordValidator['requireAtLeastOneNonAlphanumericCharacter'] ?? false) {
            $passwordConstraints[] = new Constraints\Regex(
                [
                    'pattern' => '/\W+/',
                    'message' => 'Password must contain at least one non-alphanumeric character',
                ]
            );
        }

        if (($passwordValidator['minLength'] ?? 0) > 0) {
            $passwordConstraints[] = new Constraints\Length(
                [
                    'min' => $passwordValidator['minLength'],
                ]
            );
        }

        return $passwordConstraints;
    }
}
