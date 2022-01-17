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

    /**
     * @param \Netgen\Bundle\InformationCollectionBundle\Entity\EzInfoCollectionAttribute[] $attributes
     */
    public function remove(array $attributes)
    {
        foreach ($attributes as $attribute) {
            $this->_em->remove($attribute);
        }

        $this->_em->flush();
    }

    public function findByCollectionIdAndFieldDefinitionIds($collectionId, $fieldDefinitionIds)
    {
        $qb = $this->createQueryBuilder('eica');

        return $qb->select('eica')
            ->where('eica.informationCollectionId = :collectionId')
            ->setParameter('collectionId', $collectionId)
            ->andWhere($qb->expr()->in('eica.contentClassAttributeId', ':fields'))
            ->setParameter('fields', $fieldDefinitionIds)
            ->getQuery()
            ->getResult();
    }

    public function findByCollectionId($collectionId)
    {
        $qb = $this->createQueryBuilder('eica');

        return $qb->select('eica')
            ->where('eica.informationCollectionId = :collectionId')
            ->setParameter('collectionId', $collectionId)
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
        $searchText = strtolower($searchText);

        $qb = $this->createQueryBuilder('eica');

        $result = $qb->select('eica.informationCollectionId')
            ->where('eica.contentObjectId = :contentId')
            ->setParameter('contentId', $contentId)
            ->andWhere($qb->expr()->andX(
                $qb->expr()->like('LOWER(eica.dataText)', ':searchText')
            ))
            ->setParameter('searchText', '%' . $searchText . '%')
            ->getQuery()
            ->getScalarResult();

        return array_column($result, "informationCollectionId");
    }
}
