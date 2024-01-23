<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Netgen\InformationCollection\API\Exception\RemoveAttributeFailedException;
use Netgen\InformationCollection\API\Exception\RetrieveCountException;
use Netgen\InformationCollection\API\Exception\StoringAttributeFailedException;
use Netgen\InformationCollection\API\Value\Attribute;
use Netgen\InformationCollection\API\Value\Filter\CollectionId;
use Netgen\InformationCollection\API\Value\Legacy\FieldValue;
use Netgen\InformationCollection\Doctrine\Entity\EzInfoCollection;
use Netgen\InformationCollection\Doctrine\Entity\EzInfoCollectionAttribute;

use function array_column;
use function mb_strtolower;

class EzInfoCollectionAttributeRepository extends EntityRepository
{
    /**
     * Get new \Netgen\InformationCollection\Doctrine\Entity\EzInfoCollectionAttribute instance.
     */
    public function getInstance(): EzInfoCollectionAttribute
    {
        return new EzInfoCollectionAttribute();
    }

    public function createNewFromValues(Content $content, EzInfoCollection $collection, FieldValue $fieldValue, string $fieldDefIdentifier): EzInfoCollectionAttribute
    {
        $ezInfoAttribute = $this->getInstance();

        $ezInfoAttribute->setContentObjectId($content->contentInfo->id);
        $ezInfoAttribute->setInformationCollectionId($collection->getId());
        $ezInfoAttribute->setContentClassAttributeId($fieldValue->getFieldDefinitionId());

        $field = $content->getField($fieldDefIdentifier);
        if ($field instanceof Field) {
            $ezInfoAttribute->setContentObjectAttributeId($field->id);
        }

        $ezInfoAttribute->setDataInt($fieldValue->getDataInt());
        $ezInfoAttribute->setDataFloat($fieldValue->getDataFloat());
        $ezInfoAttribute->setDataText($fieldValue->getDataText());

        return $ezInfoAttribute;
    }

    /**
     * Save object.
     *
     * @throws \Netgen\InformationCollection\API\Exception\StoringAttributeFailedException
     */
    public function save(EzInfoCollectionAttribute $infoCollectionAttribute): void
    {
        try {
            $this->_em->persist($infoCollectionAttribute);
            $this->_em->flush($infoCollectionAttribute);
        } catch (ORMException|ORMInvalidArgumentException $exception) {
            throw new StoringAttributeFailedException('', '');
        }
    }

    /**
     * @param \Netgen\InformationCollection\Doctrine\Entity\EzInfoCollectionAttribute[] $attributes
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function remove(array $attributes): void
    {
        try {
            foreach ($attributes as $attribute) {
                $this->_em->remove($attribute);
            }

            $this->_em->flush();
        } catch (ORMException|ORMInvalidArgumentException $exception) {
            throw new RemoveAttributeFailedException('', '');
        }
    }

    public function findByCollectionIdAndFieldDefinitionIds(int $collectionId, array $fieldDefinitionIds): array
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

    public function updateByCollectionId(CollectionId $collectionId, Attribute $attribute): void
    {
        $entity = $this->findOneBy([
            'informationCollectionId' => $collectionId->getCollectionId(),
            'id' => $attribute->getId(),
        ]);

        if (!$entity instanceof EzInfoCollectionAttribute) {
            throw new \InvalidArgumentException('Attribute not found.');
        }

        $entity->setDataFloat($attribute->getValue()->getDataFloat());
        $entity->setDataInt($attribute->getValue()->getDataInt());
        $entity->setDataText($attribute->getValue()->getDataText());

        $this->_em->persist($entity);
        $this->_em->flush();
    }

    public function findByCollectionId(int $collectionId): array
    {
        $qb = $this->createQueryBuilder('eica');

        return $qb->select('eica')
            ->where('eica.informationCollectionId = :collection-id')
            ->setParameter('collection-id', $collectionId)
            ->getQuery()
            ->getResult();
    }

    public function getCountByContentId(int $contentId): int
    {
        try {
            return (int) $this->createQueryBuilder('eica')
                ->andWhere('eica.contentObjectId = :contentId')
                ->setParameter('contentId', $contentId)
                ->select('COUNT(eica) as children_count')
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NonUniqueResultException|NoResultException $exception) {
            throw new RetrieveCountException('', '');
        }
    }

    public function search(int $contentId, string $searchText): array
    {
        $searchText = mb_strtolower($searchText);

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

        return array_column($result, 'informationCollectionId');
    }
}
