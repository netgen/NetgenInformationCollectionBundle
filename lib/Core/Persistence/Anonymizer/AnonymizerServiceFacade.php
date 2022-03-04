<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Persistence\Anonymizer;

use DateTimeImmutable;
use Netgen\InformationCollection\API\Persistence\Anonymizer\Anonymizer;
use Netgen\InformationCollection\Core\Persistence\ContentTypeUtils;
use Netgen\InformationCollection\Doctrine\Repository\EzInfoCollectionRepository;
use OutOfBoundsException;
use function count;

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
     */
    public function anonymize(int $contentId, array $fields = [], ?DateTimeImmutable $date = null): int
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
     */
    private function getFieldIds(int $contentId, array $fieldIdentifiers): array
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

    private function getCollections(int $contentId, ?DateTimeImmutable $date = null): array
    {
        if (null === $date) {
            $collections = $this->ezInfoCollectionRepository->findByContentId($contentId);
        } else {
            $collections = $this->ezInfoCollectionRepository->findByContentIdOlderThan($contentId, $date);
        }

        return $collections;
    }
}
