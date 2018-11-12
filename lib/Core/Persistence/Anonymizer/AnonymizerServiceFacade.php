<?php

namespace Netgen\Bundle\InformationCollectionBundle\Core\Persistence\Anonymizer;

use Netgen\Bundle\InformationCollectionBundle\API\Persistence\Anonymizer\Anonymizer;
use Netgen\Bundle\InformationCollectionBundle\Core\Persistence\ContentTypeUtils;
use Netgen\Bundle\InformationCollectionBundle\Repository\EzInfoCollectionRepository;
use DateTime;
use OutOfBoundsException;

final class AnonymizerServiceFacade
{
    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\API\Persistence\Anonymizer\Anonymizer
     */
    protected $anonymizer;

    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\Core\Persistence\ContentTypeUtils
     */
    protected $contentTypeUtils;

    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\Repository\EzInfoCollectionRepository
     */
    protected $ezInfoCollectionRepository;

    /**
     * AnonymizerServiceFacade constructor.
     *
     * @param \Netgen\Bundle\InformationCollectionBundle\API\Persistence\Anonymizer\Anonymizer $anonymizer
     * @param \Netgen\Bundle\InformationCollectionBundle\Core\Persistence\ContentTypeUtils $contentTypeUtils
     * @param \Netgen\Bundle\InformationCollectionBundle\Repository\EzInfoCollectionRepository $ezInfoCollectionRepository
     */
    public function __construct(
        Anonymizer $anonymizer,
        ContentTypeUtils $contentTypeUtils,
        EzInfoCollectionRepository $ezInfoCollectionRepository
    )
    {
        $this->anonymizer = $anonymizer;
        $this->contentTypeUtils = $contentTypeUtils;
        $this->ezInfoCollectionRepository = $ezInfoCollectionRepository;
    }

    /**
     * Anonymize collections by content id
     *
     * @param int $contentId Content id
     * @param array $fields Fields list
     * @param DateTime|null $date Anonymize collections older that this date
     *
     * @return int
     */
    public function anonymize($contentId, array $fields = [], DateTime $date = null)
    {
        $fieldsWithIds = $this->getFieldIds($contentId, $fields);

        if (!empty($fields) && empty($fieldsWithIds)) {
            return 0;
        }

        $collections = $this->getCollections($contentId, $date);

        foreach ($collections as $collection) {
            $this->anonymizer->anonymizeCollection($collection, $fields);
        }

        return count($collections);
    }

    /**
     * Map field id's to list of field identifiers
     *
     * @param int $content
     * @param array $fieldIdentifiers
     *
     * @return array
     */
    protected function getFieldIds($contentId, array $fieldIdentifiers)
    {
        $ids = [];
        foreach ($fieldIdentifiers as $identifier) {
            try {
                $ids[] = $this->contentTypeUtils->getFieldId($contentId, $identifier);
            } catch (OutOfBoundsException $e) {
                continue;
            }
        }

        return $ids;
    }

    protected function getCollections($contentId, DateTime $date = null)
    {
        if (is_null($date)) {
            $collections = $this->ezInfoCollectionRepository->findByContentId($contentId);
        } else {
            $collections = $this->ezInfoCollectionRepository->findByContentIdOlderThan($contentId, $date);
        }

        return $collections;
    }
}
