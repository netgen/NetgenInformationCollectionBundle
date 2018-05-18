<?php

namespace Netgen\Bundle\InformationCollectionBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Netgen\Bundle\InformationCollectionBundle\Entity\EzInfoCollectionAttribute;

class EzInfoCollectionAttributeRepository extends EntityRepository
{
    /**
     * Get new EzInfoCollectionAttribute instance.
     *
     * @return EzInfoCollectionAttribute
     */
    public function getInstance()
    {
        return new EzInfoCollectionAttribute();
    }

    /**
     * Save object.
     *
     * @param EzInfoCollectionAttribute $infoCollectionAttribute
     */
    public function save(EzInfoCollectionAttribute $infoCollectionAttribute)
    {
        $this->_em->persist($infoCollectionAttribute);
        $this->_em->flush($infoCollectionAttribute);
    }

    public function findByCollectionIdAndFieldDefinitionIds($collectionId, $fieldDefinitionIds)
    {
        $qb = $this->createQueryBuilder('eica');

        return $qb->select('eica')
            ->where('eica.informationCollectionId = :collection-id')
            ->setParameter('collection-id', $collectionId)
            ->andWhere($qb->expr()->in('eica.contentClassAttributeId', ':fields'))
            ->setParameter('fields', $fieldDefinitionIds)
            ->getQuery()
            ->getResult();
    }

    public function findByCollectionId($collectionId)
    {
        $qb = $this->createQueryBuilder('eica');

        return $qb->select('eica')
            ->where('eica.informationCollectionId = :collection-id')
            ->setParameter('collection-id', $collectionId)
            ->getQuery()
            ->getResult();
    }

    public function getCountByContentId($contentId)
    {
        return (int)$this->createQueryBuilder('eica')
            ->andWhere('eica.contentObjectId = :contentId')
            ->setParameter('contentId', $contentId)
            ->select('COUNT(eica) as children_count')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function search($contentId, $searchText)
    {
        $qb = $this->createQueryBuilder('eica');

        $result = $qb->select('eica.informationCollectionId')
            ->where('eica.contentObjectId = :contentId')
            ->setParameter('contentId', $contentId)
            ->andWhere('eica.dataText LIKE :searchText')
            ->setParameter('searchText', '%' . $searchText . '%')
            ->getQuery()
            ->getScalarResult();

        return array_column($result, "informationCollectionId");
    }
}
