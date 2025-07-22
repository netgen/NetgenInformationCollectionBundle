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
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Netgen\InformationCollection\API\Exception\RemoveCollectionFailedException;
use Netgen\InformationCollection\API\Exception\RetrieveCountException;
use Netgen\InformationCollection\API\Exception\StoringCollectionFailedException;
use Netgen\InformationCollection\Doctrine\Entity\EzInfoCollection;

class EzInfoCollectionRepository extends EntityRepository
{
    /**
     * Get new \Netgen\InformationCollection\Doctrine\Entity\EzInfoCollection instance.
     */
    public function getInstance(): EzInfoCollection
    {
        return new EzInfoCollection();
    }

    public function loadCollection(int $collectionId): ?EzInfoCollection
    {
        return $this->findOneBy(['id' => $collectionId]);
    }

    public function getFirstCollection(int $contentId): ?EzInfoCollection
    {
        return $this->findOneBy(
            [
                'contentObjectId' => $contentId,
            ],
            [
                'created' => 'ASC',
            ]
        );
    }

    public function getLastCollection(int $contentId): ?EzInfoCollection
    {
        return $this->findOneBy(
            [
                'contentObjectId' => $contentId,
            ],
            [
                'created' => 'DESC',
            ]
        );
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
     * @throws \Netgen\InformationCollection\API\Exception\StoringCollectionFailedException
     */
    public function save(EzInfoCollection $informationCollection): void
    {
        try {
            $this->_em->persist($informationCollection);
            $this->_em->flush($informationCollection);
        } catch (ORMException|ORMInvalidArgumentException $e) {
            throw new StoringCollectionFailedException('', '');
        }
    }

    /**
     * @throws \Netgen\InformationCollection\API\Exception\RemoveCollectionFailedException
     */
    public function remove(array $collections): void
    {
        try {
            foreach ($collections as $collection) {
                $this->_em->remove($collection);
            }

            $this->_em->flush();
        } catch (ORMException|ORMInvalidArgumentException $e) {
            throw new RemoveCollectionFailedException('', '');
        }
    }

    public function findByContentId(int $contentId): array
    {
        $qb = $this->createQueryBuilder('ezc');

        return $qb->select('ezc')
            ->where('ezc.contentObjectId = :content')
            ->setParameter('content', $contentId)
            ->getQuery()
            ->getResult();
    }

    public function findByContentIdOlderThan(int $contentId, DateTimeImmutable $date): array
    {
        $qb = $this->createQueryBuilder('ezc');

        return $qb->select('ezc')
            ->where('ezc.contentObjectId = :content')
            ->setParameter('content', $contentId)
            ->andWhere($qb->expr()->lte('ezc.created', $date->getTimestamp()))
            ->getQuery()
            ->getResult();
    }

    public function getChildrenCount(int $contentId): int
    {
        try {
            return (int) $this->createQueryBuilder('ezc')
                ->andWhere('ezc.contentObjectId = :contentId')
                ->setParameter('contentId', $contentId)
                ->select('COUNT(ezc) as children_count')
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NonUniqueResultException|NoResultException $e) {
            throw new RetrieveCountException('', '');
        }
    }

    public function filterByIntervalOfCreation(int $contentId, DateTimeInterface $startDate, DateTimeInterface $endDate, ?int $limit = null, ?int $offeset = null): array
    {

        $qb = $this->createQueryBuilder('ezc');
        $startTimestamp = $startDate->getTimestamp();
        $endTimestamp = $endDate->getTimestamp();

        $qb->select('ezc.id')
            ->where('ezc.contentObjectId = :contentId')
            ->setParameter('contentId', $contentId)
            ->andWhere($qb->expr()->andX(
                $qb->expr()->gte('ezc.created', ':startTimestamp'),
                $qb->expr()->lte('ezc.created', ':endTimestamp')
            ))
            ->setParameter('startTimestamp', $startTimestamp)
            ->setParameter('endTimestamp', $endTimestamp);

        if ($limit !== null) {
            $qb->setMaxResults($limit);
        };

        if ($offeset !== null) {
            $qb->setFirstResult($offeset);
        }

        return $qb->getQuery()
            ->getResult();
    }
}
