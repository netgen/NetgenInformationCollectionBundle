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
use Symfony\Component\Translation\TranslatorInterface;

class ExporterService implements Exporter
{
    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    protected $translator;

    /**
     * @var \Netgen\InformationCollection\Core\Persistence\ContentTypeUtils
     */
    protected $contentTypeUtils;

    /**
     * @var \Netgen\InformationCollection\API\Service\InformationCollection
     */
    protected $informationCollection;

    public function __construct(
        InformationCollection $informationCollection,
        TranslatorInterface $translator,
        ContentTypeUtils $contentTypeUtils
    ) {
        $this->translator = $translator;
        $this->contentTypeUtils = $contentTypeUtils;
        $this->informationCollection = $informationCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function export(ExportCriteria $criteria): Export
    {
        $fields = $this->contentTypeUtils
            ->getInfoCollectorFields($criteria->getContent()->id);

        $fields['created'] = $this->translator->trans('netgen_information_collection_admin_export_created', [], 'netgen_information_collection_admin');

        $collections = $this->informationCollection->getCollections(
            new ContentId($criteria->getContent()->id, 0, 100)
        );

        $rows = [];

        foreach ($collections->getCollections() as $collection) {
            $row = [];

            foreach ($fields as $fieldId => $fieldName) {
                if ($fieldId === 'created') {
                    $row[] = $collection->getCreated()->format('d-m-Y');

                    continue;
                }

                $row[] = $this->getAttributeValue((int)$fieldId, $collection->getAttributes());
            }

            $rows[] = $row;
        }

        $header = array_values($fields);

        return new Export($header, $rows);
    }

    /**
     * Get attribute value string.
     *
     * @param int $fieldId
     * @param array $attributes
     *
     * @return string
     */
    protected function getAttributeValue(int $fieldId, array $attributes)
    {
        /** @var Attribute $attribute */
        foreach ($attributes as $attribute) {
            if ($fieldId === $attribute->getFieldDefinition()->id) {
                $value = $attribute->getValue();
                $value = str_replace('"', '""', (string)$value);
                $value = str_replace(';', ', ', (string)$value);
                $value = strip_tags($value);

                $res = preg_replace(['/\r|\n/'], [' '], $value);

                if ($res === null) {
                    return '';
                }
            }
        }

        return '';
    }
}
