<?php

namespace Netgen\Bundle\InformationCollectionBundle\Repository;

use DateTime;
use Doctrine\ORM\EntityRepository;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\User\User;
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

    /**
     * @param Location $location
     * @param User $user
     *
     * @return EzInfoCollection
     */
    public function createWithValues(Location $location, User $user)
    {
        $dt = new DateTime();
        $ezInfo = $this->getInstance();

        $ezInfo->setContentObjectId($location->getContentInfo()->id);
        $ezInfo->setUserIdentifier($user->login);
        $ezInfo->setCreatorId($user->id);
        $ezInfo->setCreated($dt->getTimestamp());
        $ezInfo->setModified($dt->getTimestamp());

        return $ezInfo;
    }
}
