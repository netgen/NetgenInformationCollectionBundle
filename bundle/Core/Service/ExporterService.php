<?php

namespace Netgen\Bundle\InformationCollectionBundle\Core\Service;

use Netgen\Bundle\InformationCollectionBundle\API\Service\Exporter;
use Netgen\Bundle\InformationCollectionBundle\API\Value\Export\Export;
use Netgen\Bundle\InformationCollectionBundle\API\Value\Export\ExportCriteria;
use Netgen\Bundle\InformationCollectionBundle\Core\Persistence\ContentTypeUtils;
use Netgen\Bundle\InformationCollectionBundle\Entity\EzInfoCollection;
use Netgen\Bundle\InformationCollectionBundle\Entity\EzInfoCollectionAttribute;
use Netgen\Bundle\InformationCollectionBundle\Repository\EzInfoCollectionAttributeRepository;
use Netgen\Bundle\InformationCollectionBundle\Repository\EzInfoCollectionRepository;
use Symfony\Component\Translation\TranslatorInterface;

class ExporterService implements Exporter
{
    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\Repository\EzInfoCollectionRepository
     */
    protected $ezInfoCollectionRepository;

    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\Repository\EzInfoCollectionAttributeRepository
     */
    protected $ezInfoCollectionAttributeRepository;

    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    protected $translator;

    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\Core\Persistence\ContentTypeUtils
     */
    protected $contentTypeUtils;

    /**
     * ExporterService constructor.
     *
     * @param \Netgen\Bundle\InformationCollectionBundle\Repository\EzInfoCollectionRepository $ezInfoCollectionRepository
     * @param \Netgen\Bundle\InformationCollectionBundle\Repository\EzInfoCollectionAttributeRepository $ezInfoCollectionAttributeRepository
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     * @param \Netgen\Bundle\InformationCollectionBundle\Core\Persistence\ContentTypeUtils $contentTypeUtils
     */
    public function __construct(
        EzInfoCollectionRepository $ezInfoCollectionRepository,
        EzInfoCollectionAttributeRepository $ezInfoCollectionAttributeRepository,
        TranslatorInterface $translator,
        ContentTypeUtils $contentTypeUtils
    )
    {
        $this->ezInfoCollectionRepository = $ezInfoCollectionRepository;
        $this->ezInfoCollectionAttributeRepository = $ezInfoCollectionAttributeRepository;
        $this->translator = $translator;
        $this->contentTypeUtils = $contentTypeUtils;
    }

    /**
     * @inheritdoc
     */
    public function export(ExportCriteria $criteria)
    {
        $fields = $this->contentTypeUtils->getInfoCollectorFields($criteria->content->id);
        $fields['created'] = $this->translator->trans('netgen_information_collection_admin_export_created', [], 'netgen_information_collection_admin');

        $collections = $this->ezInfoCollectionRepository->findByCriteria($criteria);

        $rows = [];

        /** @var EzInfoCollection $collection */
        foreach ($collections as $collection) {

            $row = [];
            $attributes = $this->ezInfoCollectionAttributeRepository->findBy(['informationCollectionId' => $collection->getId()]);

            foreach ($fields as $fieldId => $fieldName) {

                if ($fieldId === 'created') {

                    $row[] = $this->getCreatedDate($collection);
                    continue;
                }

                $row[] = $this->getAttributeValue($fieldId, $attributes);

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

    /**
     * @inheritdoc
     */
    public function exportAll(ExportCriteria $criteria)
    {
        $fields = $this->contentTypeUtils->getInfoCollectorFields($criteria->content->id);
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
                    continue;
                }

                $row[] = $this->getAttributeValue($fieldId, $attributes);

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

    /**
     * Get create date from EzInfoCollection as string
     *
     * @param \Netgen\Bundle\InformationCollectionBundle\Entity\EzInfoCollection $ezInfoCollection
     *
     * @return string
     */
    protected function getCreatedDate(EzInfoCollection $ezInfoCollection)
    {
        $date = new \DateTime();
        $date->setTimestamp($ezInfoCollection->getCreated());

        return $date->format('Y-m-d');
    }

    /**
     * Get attribute value string
     *
     * @param int $fieldId
     * @param array $attributes
     *
     * @return string
     */
    protected function getAttributeValue($fieldId, $attributes)
    {
        /** @var EzInfoCollectionAttribute $attribute */
        foreach ($attributes as $attribute) {

            if ($fieldId === $attribute->getContentClassAttributeId()) {
                $value = $attribute->getValue();
                $value = str_replace('"', '""', $value);
                $value = str_replace(';', ', ', $value);
                $value = strip_tags($value);

                return preg_replace(array( '/\r|\n/' ), array( ' ' ), $value);

            }
        }

        return '';
    }
}
