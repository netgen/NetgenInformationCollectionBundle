<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Pagination;

use Netgen\InformationCollection\API\Service\InformationCollection;
use Netgen\InformationCollection\API\Value\Filter\ContentId;

class InformationCollectionCollectionListAdapter extends BaseAdapter
{
    /**
     * @var \Netgen\InformationCollection\API\Value\Filter\ContentId
     */
    protected $query;

    public function __construct(InformationCollection $informationCollectionService, ContentId $query)
    {
        $this->query = $query;
        parent::__construct($informationCollectionService);
    }

    public function getNbResults()
    {
        if (!isset($this->nbResults)) {
            $query = ContentId::countWithContentId($this->query->getContentId());

            $this->nbResults = $this->informationCollectionService
                ->getCollectionsCount($query)
                ->getCount();
        }

        return $this->nbResults;
    }

    public function getSlice($offset, $length)
    {
        $query = new ContentId($this->query->getContentId(), $offset, $length);

        $objects = $this->informationCollectionService
            ->getCollections($query)
            ->getCollections();

        $this->getNbResults();

        return $objects;
    }
}
