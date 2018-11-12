<?php

namespace Netgen\Bundle\InformationCollectionBundle\Core\Pagination;

class InformationCollectionCollectionListAdapter extends BaseAdapter
{
    public function getNbResults()
    {
        if (!isset($this->nbResults)) {
            $this->nbResults = $this->informationCollectionService
                ->getCollections($this->getCountQuery())
                ->count;
        }

        return $this->nbResults;
    }

    public function getSlice($offset, $length)
    {
        $objects = $this->informationCollectionService
            ->getCollections($this->getQuery($offset, $length))
            ->collections;

        $this->getNbResults();

        return $objects;
    }
}
