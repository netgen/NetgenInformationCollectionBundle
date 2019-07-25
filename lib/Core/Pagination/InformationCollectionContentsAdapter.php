<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Pagination;

use Netgen\InformationCollection\API\Service\InformationCollection;
use Netgen\InformationCollection\API\Value\Filter\Query;

class InformationCollectionContentsAdapter extends BaseAdapter
{
    /**
     * @var \Netgen\InformationCollection\API\Value\Filter\Query
     */
    protected $query;

    public function __construct(InformationCollection $informationCollectionService, Query $query)
    {
        $this->query = $query;
        parent::__construct($informationCollectionService);
    }

    public function getNbResults()
    {
        if (!isset($this->nbResults)) {
            $this->nbResults = $this->informationCollectionService
                ->getObjectsWithCollectionsCount()
                ->getCount();
        }

        return $this->nbResults;
    }

    public function getSlice($offset, $length)
    {
        $query = new Query($offset, $length);

        $objects = $this->informationCollectionService
            ->getObjectsWithCollections($query)
            ->getContents();

        $this->getNbResults();

        return $objects;
    }
}
