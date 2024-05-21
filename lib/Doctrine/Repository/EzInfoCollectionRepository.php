<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Doctrine\Repository;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\User\User;
use Netgen\InformationCollection\API\Exception\RemoveCollectionFailedException;
use Netgen\InformationCollection\API\Exception\RetrieveCountException;
use Netgen\InformationCollection\API\Exception\StoringCollectionFailedException;
use Netgen\InformationCollection\Doctrine\Entity\EzInfoCollection;

class EzInfoCollectionRepository extends EntityRepository
{
    /**
     * Get new \Netgen\InformationCollection\Doctrine\Entity\EzInfoCollection instance.
     *
     * @return \Netgen\InformationCollection\Doctrine\Entity\EzInfoCollection
     */
    public function getInstance()
    {
        return new EzInfoCollection();
    }

    public function loadCollection(int $collectionId)
    {
        $collection = $this->findOneBy(['id' => $collectionId]);

        if ($collection instanceof EzInfoCollection) {
            return $collection;
        }
    }

    public function getFirstCollection(int $contentId): EzInfoCollection
    {
        $collection = $this->findOneBy(
            [
                'contentObjectId' => $contentId,
            ],
            [
                'created' => 'ASC',
            ]
        );

        if ($collection instanceof EzInfoCollection) {
            return $collection;
        }
    }

    public function getLastCollection(int $contentId): EzInfoCollection
    {
        $collection = $this->findOneBy(
            [
                'contentObjectId' => $contentId,
            ],
            [
                'created' => 'DESC',
            ]
        );

        if ($collection instanceof EzInfoCollection) {
            return $collection;
        }
    }

    public function createNewFromValues(Content $content, User $user): EzInfoCollection
    {
        $ezInfo = $this->getInstance();
        $dt = new DateTimeImmutable();

        $ezInfo->setContentObjectId($content->contentInfo->id);
        $ezInfo->setUserIdentifier($user->login);
        $ezInfo->setCreatorId($user->id);
        $ezInfo->setCreated($dt->getTimestamp());
        $ezInfo->setModified($dt->getTimestamp());

        return $ezInfo;
    }

    /**
     * Save object.
     *
     * @param \Netgen\InformationCollection\Doctrine\Entity\EzInfoCollection $informationCollection
     *
     * @throws StoringCollectionFailedException
     */
    public function save(EzInfoCollection $informationCollection)
    {
        try {
            $this->_em->persist($informationCollection);
            $this->_em->flush($informationCollection);
        } catch (ORMException | ORMInvalidArgumentException $e) {
            throw new StoringCollectionFailedException('', '');
        }
    }

    /**
     * @param array $collections
     *
     * @throws RemoveCollectionFailedException
     */
    public function remove(array $collections)
    {
        try {
            foreach ($collections as $collection) {
                $this->_em->remove($collection);
            }

            $this->_em->flush();
        } catch (ORMException | ORMInvalidArgumentException $e) {
            throw new RemoveCollectionFailedException('', '');
        }
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

    public function findByContentIdOlderThan($contentId, DateTimeImmutable $date)
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
        try {
            return (int) $this->createQueryBuilder('ezc')
                ->andWhere('ezc.contentObjectId = :contentId')
                ->setParameter('contentId', $contentId)
                ->select('COUNT(ezc) as children_count')
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NonUniqueResultException | NoResultException $e) {
            throw new RetrieveCountException('', '');
        }
    }

    public function filterByIntervalOfCreation(int $contentId, DateTimeInterface $startDate, DateTimeInterface $endDate): array
    {

        $qb = $this->createQueryBuilder('ezc');
        $startTimestamp = $startDate->getTimestamp();
        $endTimestamp = $endDate->getTimestamp();

        return $qb->select('ezc.id')
            ->where('ezc.contentObjectId = :contentId')
            ->setParameter('contentId', $contentId)
            ->andWhere($qb->expr()->andX(
                $qb->expr()->gte('ezc.created', ':startTimestamp'),
                $qb->expr()->lte('ezc.created', ':endTimestamp')
            ))
            ->setParameter('startTimestamp', $startTimestamp)
            ->setParameter('endTimestamp', $endTimestamp)
            ->getQuery()
            ->getResult();
    }
}
