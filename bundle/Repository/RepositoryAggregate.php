<?php

namespace Netgen\Bundle\InformationCollectionBundle\Repository;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\User\User;
use Netgen\Bundle\InformationCollectionBundle\Entity\EzInfoCollection;
use Netgen\Bundle\InformationCollectionBundle\Entity\EzInfoCollectionAttribute;
use Netgen\Bundle\InformationCollectionBundle\Value\LegacyData;

class RepositoryAggregate
{
    /**
     * @var EzInfoCollectionRepository
     */
    protected $infoCollectionRepository;

    /**
     * @var EzInfoCollectionAttributeRepository
     */
    protected $infoCollectionAttributeRepository;

    /**
     * RepositoryAggregate constructor.
     *
     * @param EzInfoCollectionRepository $infoCollectionRepository
     * @param EzInfoCollectionAttributeRepository $infoCollectionAttributeRepository
     */
    public function __construct(
        EzInfoCollectionRepository $infoCollectionRepository,
        EzInfoCollectionAttributeRepository $infoCollectionAttributeRepository
    ) {
        $this->infoCollectionRepository = $infoCollectionRepository;
        $this->infoCollectionAttributeRepository = $infoCollectionAttributeRepository;
    }

    /**
     * Creates EzInfoCollection instance.
     *
     * @param Location $location
     * @param User $currentUser
     *
     * @return \Netgen\Bundle\InformationCollectionBundle\Entity\EzInfoCollection
     */
    public function createMain(Location $location, User $currentUser)
    {
        $main = $this->infoCollectionRepository->createWithValues($location, $currentUser);

        $this->infoCollectionRepository->save($main);

        return $main;
    }

    /**
     * Creates EzInfoCollectionAttribute instance.
     *
     * @param Location $location
     * @param EzInfoCollection $ezInfoCollection
     * @param int $fieldId
     * @param LegacyData $value
     *
     * @return \Netgen\Bundle\InformationCollectionBundle\Entity\EzInfoCollectionAttribute
     */
    public function createChild(Location $location, EzInfoCollection $ezInfoCollection, $fieldId, LegacyData $value)
    {
        $child = $this->infoCollectionAttributeRepository
            ->createWithValues($location, $ezInfoCollection, $fieldId, $value);

        $this->infoCollectionAttributeRepository->save($child);

        return $child;
    }
}
