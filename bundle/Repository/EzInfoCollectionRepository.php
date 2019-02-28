<?php

namespace Netgen\Bundle\InformationCollectionBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Netgen\Bundle\InformationCollectionBundle\API\Value\Export\ExportCriteria;
use Netgen\Bundle\InformationCollectionBundle\Entity\EzInfoCollection;
use DateTime;

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

    public function findByContentIdOlderThan($contentId, DateTime $date)
    {
        $qb = $this->createQueryBuilder('ezc');

        return $qb->select('ezc')
            ->where('ezc.contentObjectId = :content')
            ->setParameter('content', $contentId)
            ->andWhere($qb->expr()->lte('ezc.created', $date->getTimestamp()))
            ->getQuery()
            ->getResult();
    }

    public function findByCriteria(ExportCriteria $criteria)
    {
        if (empty($criteria->content->id)) {
            throw new \RuntimeException('Content id is not valid or does not exist');
        }

        $qb = $this->createQueryBuilder('ezc');

        $qb->select('ezc')
           ->where('ezc.contentObjectId = :content')
           ->setParameter('content', $criteria->content->id);

        if (!empty($criteria->from) && !empty($criteria->to)) {
            $dateFrom = $criteria->from->getTimestamp();
            $dateTo = $criteria->to->getTimestamp();

            if ($dateFrom > $dateTo) {
                throw new \RuntimeException("Starting date must be greater than ending date");
            }
            $qb->andWhere('ezc.created BETWEEN :from AND :to')
               ->setParameter('from', $dateFrom)
               ->setParameter('to', $dateTo);
        }

        return $qb->getQuery()->getResult();
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
