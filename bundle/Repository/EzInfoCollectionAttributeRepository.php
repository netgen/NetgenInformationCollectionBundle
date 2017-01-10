<?php

namespace Netgen\Bundle\InformationCollectionBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Netgen\Bundle\InformationCollectionBundle\Entity\EzInfoCollectionAttribute;

class EzInfoCollectionAttributeRepository extends EntityRepository
{
    /**
     * Get new EzInfoCollectionAttribute instance
     *
     * @return EzInfoCollectionAttribute
     */
    public function getInstance()
    {
        return new EzInfoCollectionAttribute();
    }

    /**
     * Save object
     *
     * @param EzInfoCollectionAttribute $infoCollectionAttribute
     */
    public function save(EzInfoCollectionAttribute $infoCollectionAttribute)
    {
        $this->_em->persist($infoCollectionAttribute);
        $this->_em->flush($infoCollectionAttribute);
    }
}
