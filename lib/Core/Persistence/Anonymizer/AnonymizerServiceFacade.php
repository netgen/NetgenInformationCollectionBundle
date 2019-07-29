<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Persistence\Anonymizer;

use DateTime;
use Netgen\InformationCollection\API\Persistence\Anonymizer\Anonymizer;
use Netgen\InformationCollection\Core\Persistence\ContentTypeUtils;
use Netgen\InformationCollection\Doctrine\Repository\EzInfoCollectionRepository;
use OutOfBoundsException;

final class AnonymizerServiceFacade
{
    /**
     * @var \Netgen\InformationCollection\API\Persistence\Anonymizer\Anonymizer
     */
    private $anonymizer;

    /**
     * @var \Netgen\InformationCollection\Core\Persistence\ContentTypeUtils
     */
    private $contentTypeUtils;

    /**
     * @var \Netgen\InformationCollection\Doctrine\Repository\EzInfoCollectionRepository
     */
    private $ezInfoCollectionRepository;

    /**
     * AnonymizerServiceFacade constructor.
     *
     * @param \Netgen\InformationCollection\API\Persistence\Anonymizer\Anonymizer $anonymizer
     * @param \Netgen\InformationCollection\Core\Persistence\ContentTypeUtils $contentTypeUtils
     * @param \Netgen\InformationCollection\Doctrine\Repository\EzInfoCollectionRepository $ezInfoCollectionRepository
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
     * @param \DateTimeImmutable|null $date Anonymize collections older that this date
     *
     * @return int
     */
    public function anonymize($contentId, array $fields = [], ?\DateTimeImmutable $date = null)
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
     * @param int $contentId
     * @param array $fieldIdentifiers
     *
     * @return array
     */
    private function getFieldIds(int $contentId, array $fieldIdentifiers)
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
