<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class InformationCollectionCollector extends DataCollector
{
    public function collect(Request $request, Response $response, \Throwable $exception = null)
    {
        $collectedItemsCount = 0;
        $collectedItems = null;

        if ($request->get('information_collection') !== null) {
            $collectedItems = $request->get('information_collection')['fieldsData'];
            $collectedItemsCount = count($collectedItems);
        }

        $this->data = [
            'collected_items' => $collectedItems,
            'collected_items_count' => $collectedItemsCount,
        ];
    }

    public function reset(): void
    {
        $this->data = [];
    }

    public function getName(): string
    {
        return 'netgen_information_collection_collector';
    }

    public function getCollectedItems(): array
    {
        return $this->data['collected_items'];
    }

    public function getCollectedItemsCount(): int
    {
        return $this->data['collected_items_count'];
    }
}
