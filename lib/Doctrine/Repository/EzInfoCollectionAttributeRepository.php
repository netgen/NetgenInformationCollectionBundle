<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Field;
use Netgen\InformationCollection\API\Exception\RemoveAttributeFailedException;
use Netgen\InformationCollection\API\Exception\RetrieveCountException;
use Netgen\InformationCollection\API\Exception\StoringAttributeFailedException;
use Netgen\InformationCollection\API\Value\Attribute;
use Netgen\InformationCollection\API\Value\Filter\CollectionId;
use Netgen\InformationCollection\API\Value\Legacy\FieldValue;
use Netgen\InformationCollection\Doctrine\Entity\EzInfoCollection;
use Netgen\InformationCollection\Doctrine\Entity\EzInfoCollectionAttribute;

class EzInfoCollectionAttributeRepository extends EntityRepository
{
    /**
     * Get new \Netgen\InformationCollection\Doctrine\Entity\EzInfoCollectionAttribute instance.
     *
     * @return \Netgen\InformationCollection\Doctrine\Entity\EzInfoCollectionAttribute
     */
    public function getInstance()
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
     * @param \Netgen\InformationCollection\Doctrine\Entity\EzInfoCollectionAttribute $infoCollectionAttribute
     *
     * @throws StoringAttributeFailedException
     */
    public function save(EzInfoCollectionAttribute $infoCollectionAttribute)
    {
        try {
            $this->_em->persist($infoCollectionAttribute);
            $this->_em->flush($infoCollectionAttribute);
        } catch (ORMException | ORMInvalidArgumentException $exception) {
            throw new StoringAttributeFailedException('', '');
        }
    }

    /**
     * @param \Netgen\InformationCollection\Doctrine\Entity\EzInfoCollectionAttribute[] $attributes
     *
     * @throws ORMException
     */
    public function remove(array $attributes)
    {
        try {
            foreach ($attributes as $attribute) {
                $this->_em->remove($attribute);
            }

            $this->_em->flush();
        } catch (ORMException | ORMInvalidArgumentException $exception) {
            throw  new RemoveAttributeFailedException('', '');
        }
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

    public function updateByCollectionId(CollectionId $collectionId, Attribute $attribute)
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
        try {
            return (int) $this->createQueryBuilder('eica')
                ->andWhere('eica.contentObjectId = :contentId')
                ->setParameter('contentId', $contentId)
                ->select('COUNT(eica) as children_count')
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NonUniqueResultException | NoResultException $exception) {
            throw new RetrieveCountException('', '');
        }
    }

    public function search($contentId, $searchText)
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
