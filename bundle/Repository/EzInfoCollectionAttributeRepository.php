<?php

namespace Netgen\Bundle\InformationCollectionBundle\Repository;

use Doctrine\ORM\EntityRepository;
use eZ\Publish\API\Repository\Values\Content\Location;
use Netgen\Bundle\InformationCollectionBundle\Entity\EzInfoCollection;
use Netgen\Bundle\InformationCollectionBundle\Entity\EzInfoCollectionAttribute;
use Netgen\Bundle\InformationCollectionBundle\Value\LegacyData;

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
     * @param Location $location
     * @param EzInfoCollection $ezInfoCollection
     * @param $fieldId
     * @param LegacyData $value
     *
     * @return EzInfoCollectionAttribute
     */
    public function createWithValues(Location $location, EzInfoCollection $ezInfoCollection, $fieldId, LegacyData $value)
    {
        $ezInfoAttribute = $this->getInstance();
        $ezInfoAttribute->setContentObjectId($location->getContentInfo()->id);
        $ezInfoAttribute->setInformationCollectionId($ezInfoCollection->getId());
        $ezInfoAttribute->setContentClassAttributeId($value->getContentClassAttributeId());
        $ezInfoAttribute->setContentObjectAttributeId($fieldId);
        $ezInfoAttribute->setDataInt($value->getDataInt());
        $ezInfoAttribute->setDataFloat($value->getDataFloat());
        $ezInfoAttribute->setDataText($value->getDataText());

        return $ezInfoAttribute;
    }
}
