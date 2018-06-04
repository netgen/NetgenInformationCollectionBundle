<?php

namespace Netgen\Bundle\InformationCollectionBundle\Core\Pagination;

class InformationCollectionContentsAdapter extends BaseAdapter
{
    public function getNbResults()
    {
        if (!isset($this->nbResults)) {
            $this->nbResults = $this->informationCollectionService
                ->getObjectsWithCollections($this->getCountQuery())
                ->count;
        }

        return $this->nbResults;
    }

    public function getSlice($offset, $length)
    {
        $objects = $this->informationCollectionService
            ->getObjectsWithCollections($this->getQuery($offset, $length))
            ->contents;

        $this->getNbResults();

        return $objects;
    }
}
