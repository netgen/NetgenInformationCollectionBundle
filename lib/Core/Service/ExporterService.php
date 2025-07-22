<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Service;

use Netgen\InformationCollection\API\Service\Exporter;
use Netgen\InformationCollection\API\Service\InformationCollection;
use Netgen\InformationCollection\API\Value\Attribute;
use Netgen\InformationCollection\API\Value\Export\Export;
use Netgen\InformationCollection\API\Value\Export\ExportCriteria;
use Netgen\InformationCollection\API\Value\Filter\ContentId;
use Netgen\InformationCollection\Core\Persistence\ContentTypeUtils;
use Symfony\Contracts\Translation\TranslatorInterface;
use function array_values;
use function preg_replace;
use function str_replace;
use function strip_tags;

class ExporterService implements Exporter
{
    protected TranslatorInterface $translator;

    protected ContentTypeUtils $contentTypeUtils;

    protected InformationCollection $informationCollection;

    public function __construct(
        InformationCollection $informationCollection,
        TranslatorInterface $translator,
        ContentTypeUtils $contentTypeUtils
    ) {
        $this->translator = $translator;
        $this->contentTypeUtils = $contentTypeUtils;
        $this->informationCollection = $informationCollection;
    }

    public function export(ExportCriteria $criteria): Export
    {
        $fields = $this->contentTypeUtils
            ->getInfoCollectorFields($criteria->getContentId()->getContentId());

        $fields['created'] = $this->translator->trans('netgen_information_collection_admin_export_created', [], 'netgen_information_collection_admin');

        $collections = $this->informationCollection->filterCollections($criteria);

        $rows = [];

        foreach ($collections->getCollections() as $collection) {
            $row = [];

            foreach ($fields as $fieldId => $fieldName) {
                if ($fieldId === 'created') {
                    $row[] = $collection->getCreated()->format('d-m-Y');

                    continue;
                }

                $row[] = $this->getAttributeValue((int) $fieldId, $collection->getAttributes());
            }

            $rows[] = $row;
        }

        $header = array_values($fields);

        return new Export($header, $rows);
    }

    /**
     * Get attribute value string.
     */
    protected function getAttributeValue(int $fieldId, array $attributes): string
    {
        /** @var Attribute $attribute */
        foreach ($attributes as $attribute) {
            if ($fieldId === $attribute->getFieldDefinition()->id) {
                $value = $attribute->getValue();
                $value = str_replace('"', '""', (string) $value);
                $value = str_replace(';', ', ', (string) $value);
                $value = strip_tags($value);

                $res = preg_replace(['/\r|\n/'], [' '], $value);

                return $res ?? '';
            }
        }

        return '';
    }
}
