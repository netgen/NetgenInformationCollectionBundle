<?php

namespace Netgen\Bundle\InformationCollectionBundle\Core\Service;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use Netgen\Bundle\InformationCollectionBundle\API\Service\Exporter;
use Netgen\Bundle\InformationCollectionBundle\API\Value\Export\Export;
use Netgen\Bundle\InformationCollectionBundle\API\Value\Export\ExportCriteria;
use Netgen\Bundle\InformationCollectionBundle\Entity\EzInfoCollection;
use Netgen\Bundle\InformationCollectionBundle\Entity\EzInfoCollectionAttribute;
use Netgen\Bundle\InformationCollectionBundle\Repository\EzInfoCollectionAttributeRepository;
use Netgen\Bundle\InformationCollectionBundle\Repository\EzInfoCollectionRepository;
use Symfony\Component\Translation\TranslatorInterface;

class ExporterService implements Exporter
{
    /**
     * @var EzInfoCollectionRepository
     */
    protected $ezInfoCollectionRepository;

    /**
     * @var EzInfoCollectionAttributeRepository
     */
    protected $ezInfoCollectionAttributeRepository;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        EzInfoCollectionRepository $ezInfoCollectionRepository,
        EzInfoCollectionAttributeRepository $ezInfoCollectionAttributeRepository,
        TranslatorInterface $translator
    )
    {
        $this->ezInfoCollectionRepository = $ezInfoCollectionRepository;
        $this->ezInfoCollectionAttributeRepository = $ezInfoCollectionAttributeRepository;
        $this->translator = $translator;
    }

    public function export(ExportCriteria $criteria)
    {
        $fields = $this->getFields($criteria->contentType);
        $fields['created'] = $this->translator->trans('netgen_information_collection_admin_export_created', [], 'netgen_information_collection_admin');

        $collections = $this->ezInfoCollectionRepository->findBy(['contentObjectId' => $criteria->content->id]);

        $rows = [];

        /** @var EzInfoCollection $collection */
        foreach ($collections as $collection) {

            $row = [];
            $attributes = $this->ezInfoCollectionAttributeRepository->findBy(['informationCollectionId' => $collection->getId()]);

            foreach ($fields as $fieldId => $fieldName) {

                if ($fieldId === 'created') {

                    $row[] = $this->getCreatedDate($collection);
                }

                /** @var EzInfoCollectionAttribute $attribute */
                foreach ($attributes as $attribute) {

                    if ($fieldId === $attribute->getContentClassAttributeId()) {

                        $row[] = $attribute->getValue();

                    } else {
                        continue;
                    }

                }

            }

            $rows[] = $row;

        }

        $header = array_values($fields);

        return new Export(
            [
                'header' => $header,
                'contents' => $rows,
            ]
        );
    }

    protected function getFields(ContentType $contentType)
    {
        $fields = [];
        foreach ($contentType->getFieldDefinitions() as $fieldDefinition) {

            if ($fieldDefinition->isInfoCollector) {
                $fields[$fieldDefinition->id] = $fieldDefinition->getName();
            }
        }

        return $fields;
    }

    protected function getCreatedDate(EzInfoCollection $ezInfoCollection)
    {
        $date = new \DateTime();
        $date->setTimestamp($ezInfoCollection->getCreated());

        return $date->format('Y-m-d');
    }
}
