<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Netgen\InformationCollection\Doctrine\Entity\EzInfoCollection;

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

    public function findByContentIdOlderThan($contentId, \DateTimeImmutable $date)
    {
        $qb = $this->createQueryBuilder('ezc');

        return $qb->select('ezc')
            ->where('ezc.contentObjectId = :content')
            ->setParameter('content', $contentId)
            ->andWhere($qb->expr()->lte('ezc.created', $date->getTimestamp()))
            ->getQuery()
            ->getResult();
    }

    public function getChildrenCount($contentId)
    {
        return (int) $this->createQueryBuilder('ezc')
            ->andWhere('ezc.contentObjectId = :contentId')
            ->setParameter('contentId', $contentId)
            ->select('COUNT(ezc) as children_count')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
