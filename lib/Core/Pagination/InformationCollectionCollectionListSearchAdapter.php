<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Pagination;

use Netgen\InformationCollection\API\Service\InformationCollection;
use Netgen\InformationCollection\API\Value\Filter\SearchCountQuery;
use Netgen\InformationCollection\API\Value\Filter\SearchQuery;

class InformationCollectionCollectionListSearchAdapter extends BaseAdapter
{
    /**
     * @var \Netgen\InformationCollection\API\Value\Filter\SearchQuery
     */
    protected $query;

    public function __construct(InformationCollection $informationCollectionService, SearchQuery $query)
    {
        $this->query = $query;
        parent::__construct($informationCollectionService);
    }

    public function getNbResults(): int
    {
        if (!isset($this->nbResults)) {
            $query = new SearchCountQuery(
                $this->query->getContentId(),
                $this->query->getSearchText(),
                0,
                0
            );

            $this->nbResults = $this->informationCollectionService
                ->searchCount($query)
                ->getCount();
        }

        return $this->nbResults;
    }

    public function getSlice($offset, $length): iterable
    {
        $query = new SearchQuery(
            $this->query->getContentId(),
            $this->query->getSearchText(),
            $offset,
            $length
        );

        $objects = $this->informationCollectionService
            ->search($query)
            ->getCollections();

        $this->getNbResults();

        return $objects;
    }
}
