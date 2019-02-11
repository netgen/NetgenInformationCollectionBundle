<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Core\Persistence\Anonymizer;

use DateTime;
use Netgen\Bundle\InformationCollectionBundle\API\Persistence\Anonymizer\Anonymizer;
use Netgen\Bundle\InformationCollectionBundle\Core\Persistence\ContentTypeUtils;
use Netgen\Bundle\InformationCollectionBundle\Repository\EzInfoCollectionRepository;
use OutOfBoundsException;

final class AnonymizerServiceFacade
{
    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\API\Persistence\Anonymizer\Anonymizer
     */
    private $anonymizer;

    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\Core\Persistence\ContentTypeUtils
     */
    private $contentTypeUtils;

    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\Repository\EzInfoCollectionRepository
     */
    private $ezInfoCollectionRepository;

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
    ) {
        $this->anonymizer = $anonymizer;
        $this->contentTypeUtils = $contentTypeUtils;
        $this->ezInfoCollectionRepository = $ezInfoCollectionRepository;
    }

    /**
     * Anonymize collections by content id.
     *
     * @param int $contentId Content id
     * @param array $fields Fields list
     * @param DateTime|null $date Anonymize collections older that this date
     *
     * @return int
     */
    public function anonymize($contentId, array $fields = [], \DateTimeImmutable $date = null)
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
     * Map field id's to list of field identifiers.
     *
     * @param int $content
     * @param array $fieldIdentifiers
     * @param mixed $contentId
     *
     * @return array
     */
    private function getFieldIds($contentId, array $fieldIdentifiers)
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

    private function getCollections($contentId, \DateTimeImmutable $date = null)
    {
        if (null === $date) {
            $collections = $this->ezInfoCollectionRepository->findByContentId($contentId);
        } else {
            $collections = $this->ezInfoCollectionRepository->findByContentIdOlderThan($contentId, $date);
        }

        return $collections;
    }
}
