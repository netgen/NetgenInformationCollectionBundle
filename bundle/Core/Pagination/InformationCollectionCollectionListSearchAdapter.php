<?php

namespace Netgen\Bundle\InformationCollectionBundle\Core\Pagination;

class InformationCollectionCollectionListSearchAdapter extends BaseAdapter
{
    public function getNbResults()
    {
        if (!isset($this->nbResults)) {
            $this->nbResults = $this->informationCollectionService
                ->search($this->getCountQuery())
                ->count;
        }

        return $this->nbResults;
    }

    public function getSlice($offset, $length)
    {
        $objects = $this->informationCollectionService
            ->search($this->getQuery($offset, $length))
            ->collections;

        $this->getNbResults();

        return $objects;
    }
}
