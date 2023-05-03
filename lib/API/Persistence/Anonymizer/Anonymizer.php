<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Persistence\Anonymizer;

interface Anonymizer
{
    /**
     * Anonymizes collection with option to anonymize only fields identified by passed ids.
     */
    public function anonymizeCollection(int $collection, array $fields = []): void;
}
