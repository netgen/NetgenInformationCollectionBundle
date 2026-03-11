<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Form\FieldTypeHandler;

use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\BinaryFile\Value as FileValue;
use Netgen\Bundle\InformationCollectionBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

final class BinaryFile extends FieldTypeHandler
{
    public function convertFieldValueToForm(Value $value, ?FieldDefinition $fieldDefinition = null): mixed
    {
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile|null $data
     */
    public function convertFieldValueFromForm(mixed $data): ?FileValue
    {
        if ($data === null) {
            return null;
        }

        $fileData = [
            'inputUri' => $data->getRealPath(),
            'fileName' => $data->getClientOriginalName(),
            'fileSize' => $data->getSize(),
        ];

        return new FileValue($fileData);
    }

    protected function buildFieldForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        string $languageCode,
        ?Content $content = null
    ): void {
        $options = $this->getDefaultFieldOptions($fieldDefinition, $languageCode, $content);
        $maxFileSize = $fieldDefinition->validatorConfiguration['FileSizeValidator']['maxFileSize'] ?? false;

        if ($maxFileSize !== false) {
            $options['constraints'][] = new Constraints\File(
                [
                    'maxSize' => $maxFileSize * Constraints\FileValidator::MB_BYTES,
                ]
            );
        }

        // Used with update for displaying current file
        $options['block_name'] = 'ibexa_forms_binary_file';

        $formBuilder->add($fieldDefinition->identifier, FileType::class, $options);
    }
}
