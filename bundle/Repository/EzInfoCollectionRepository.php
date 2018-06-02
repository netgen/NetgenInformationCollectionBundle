<?php

namespace Netgen\Bundle\InformationCollectionBundle\Repository;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use Netgen\Bundle\InformationCollectionBundle\Entity\EzInfoCollection;

class EzInfoCollectionRepository extends EntityRepository
{
    /**
     * Get new EzInfoCollection instance.
     *
     * @return EzInfoCollection
     */
    public function getInstance()
    {
        return new EzInfoCollection();
    }

    /**
     * Save object.
     *
     * @param EzInfoCollection $informationCollection
     */
    public function save(EzInfoCollection $informationCollection)
    {
        $this->_em->persist($informationCollection);
        $this->_em->flush($informationCollection);
    }

    public function remove(array $collections)
    {
        foreach ($collections as $collection) {
            $this->_em->remove($collection);
        }

        $this->_em->flush();
    }

    public function findByContentId($contentId)
    {
        $qb = $this->createQueryBuilder('ezc');

        return $qb->select('ezc')
            ->where('ezc.contentObjectId = :content')
            ->setParameter('content', $contentId)
            ->getQuery()
            ->getResult();
    }

    public function findOlderThanThirtyDaysByContentId($contentId)
    {
        $qb = $this->createQueryBuilder('ezc');
        $date = new \DateTime();
        $date->modify('-30 days');

        return $qb->select('ezc')
            ->where('ezc.contentObjectId = :content')
            ->setParameter('content', $contentId)
            ->andWhere('ezc.created > :date')
            ->setParameter('date', $date->getTimestamp())
            ->getQuery()
            ->getResult();
    }

    public function findOlderThanThirtyDaysById($infoCollectionId)
    {
        $qb = $this->createQueryBuilder('ezc');
        $date = new \DateTime();
        $date->modify('-30 days');

        return $qb->select('ezc')
            ->where('ezc.id = :info-collection')
            ->setParameter('info-collection', $infoCollectionId)
            ->andWhere('ezc.created > :date')
            ->setParameter('date', $date->getTimestamp())
            ->getQuery()
            ->getSingleResult(AbstractQuery::HYDRATE_OBJECT);
    }

    public function overview()
    {
        $qb = $this->createQueryBuilder('ezc');

        return $qb->select('COUNT(ezc) as count')
            ->a
            ->where('ezc.id = :info-collection')
            ->setParameter('info-collection', $infoCollectionId)
            ->andWhere('ezc.created > :date')
            ->setParameter('date', $date->getTimestamp())
            ->getQuery()
            ->getSingleResult(AbstractQuery::HYDRATE_OBJECT);
    }

    public function getChildrenCount($contentId)
    {
        return (int)$this->createQueryBuilder('ezc')
            ->andWhere('ezc.contentObjectId = :contentId')
            ->setParameter('contentId', $contentId)
            ->select('COUNT(ezc) as children_count')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
