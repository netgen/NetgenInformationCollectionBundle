<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Ibexa\ContentForms;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Validator\ContentValidator;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;

final class InfoCollectorContentValidator implements ContentValidator
{
    public function supports(ValueObject $object): bool
    {
        return true;
    }

    public function validate(ValueObject $object, array $context = [], ?array $fieldIdentifiers = null): array
    {
        $content = $context['content'] ?? null;
        if (!$content instanceof Content) {
            return [];
        }

        foreach ($content->getContentType()->fieldDefinitions as $fieldDefinition) {
            /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition $fieldDefinition */
            if ($fieldDefinition->isInfoCollector) {
                (function () {
                    $this->isRequired = false;
                })->call($fieldDefinition);
            }
        }

        return [];
    }
}
