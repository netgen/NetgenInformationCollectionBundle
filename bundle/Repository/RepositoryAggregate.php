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
    protected $repository;

    /**
     * @var EzInfoCollectionAttributeRepository
     */
    protected $attributeRepository;

    /**
     * RepositoryAggregate constructor.
     *
     * @param EzInfoCollectionRepository $repository
     * @param EzInfoCollectionAttributeRepository $attributeRepository
     */
    public function __construct(
        EzInfoCollectionRepository $repository,
        EzInfoCollectionAttributeRepository $attributeRepository
    ) {
        $this->repository = $repository;
        $this->attributeRepository = $attributeRepository;
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
        $main = $this->repository->createWithValues($location, $currentUser);

        $this->repository->save($main);

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
        $child = $this->attributeRepository
            ->createWithValues($location, $ezInfoCollection, $fieldId, $value);

        $this->attributeRepository->save($child);

        return $child;
    }
}
