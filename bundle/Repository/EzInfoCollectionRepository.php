<?php

namespace Netgen\Bundle\InformationCollectionBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Netgen\Bundle\InformationCollectionBundle\Entity\EzInfoCollection;

class EzInfoCollectionRepository extends EntityRepository
{
    /**
     * Get new EzInfoCollection instance
     *
     * @return EzInfoCollection
     */
    public function getInstance()
    {
        return new EzInfoCollection();
    }

    /**
     * Save object
     *
     * @param EzInfoCollection $informationCollection
     */
    public function save(EzInfoCollection $informationCollection)
    {
        $this->_em->persist($informationCollection);
        $this->_em->flush($informationCollection);
    }
}
