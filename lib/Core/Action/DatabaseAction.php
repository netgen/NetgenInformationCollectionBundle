<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Action;

use Doctrine\DBAL\DBALException;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\Repository;
use Netgen\InformationCollection\Doctrine\Entity\EzInfoCollection;
use Netgen\InformationCollection\API\Value\Event\InformationCollected;
use Netgen\InformationCollection\API\Exception\ActionFailedException;
use Netgen\InformationCollection\Core\Factory\FieldDataFactory;
use Netgen\InformationCollection\Doctrine\Repository\EzInfoCollectionAttributeRepository;
use Netgen\InformationCollection\Doctrine\Repository\EzInfoCollectionRepository;
use Netgen\InformationCollection\API\Action\ActionInterface;
use Netgen\InformationCollection\API\Action\CrucialActionInterface;
use Netgen\InformationCollection\API\Value\Legacy\FieldValue;

class DatabaseAction implements ActionInterface, CrucialActionInterface
{
    /**
     * @var \Netgen\InformationCollection\Core\Factory\FieldDataFactory
     */
    protected $factory;

    /**
     * @var \Netgen\InformationCollection\Doctrine\Repository\EzInfoCollectionRepository
     */
    protected $infoCollectionRepository;

    /**
     * @var \Netgen\InformationCollection\Doctrine\Repository\EzInfoCollectionAttributeRepository
     */
    protected $infoCollectionAttributeRepository;

    /**
     * @var \eZ\Publish\API\Repository\Repository
     */
    protected $repository;
    /**
     * @var \eZ\Publish\API\Repository\PermissionResolver
     */
    private $permissionResolver;

    /**
     * PersistToDatabaseAction constructor.
     *
     * @param \Netgen\InformationCollection\Core\Factory\FieldDataFactory $factory
     * @param \Netgen\InformationCollection\Doctrine\Repository\EzInfoCollectionRepository $infoCollectionRepository
     * @param \Netgen\InformationCollection\Doctrine\Repository\EzInfoCollectionAttributeRepository $infoCollectionAttributeRepository
     * @param \eZ\Publish\API\Repository\Repository $repository
     */
    public function __construct(
        FieldDataFactory $factory,
        EzInfoCollectionRepository $infoCollectionRepository,
        EzInfoCollectionAttributeRepository $infoCollectionAttributeRepository,
        Repository $repository,
        PermissionResolver $permissionResolver
    ) {
        $this->factory = $factory;
        $this->infoCollectionRepository = $infoCollectionRepository;
        $this->infoCollectionAttributeRepository = $infoCollectionAttributeRepository;
        $this->repository = $repository;
        $this->permissionResolver = $permissionResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function act(InformationCollected $event): void
    {
        $struct = $event->getInformationCollectionStruct();
        $contentType = $event->getContentType();
        $location = $event->getLocation();
        $content = $event->getContent();

        $userReference = $this->permissionResolver
            ->getCurrentUserReference();

        $user = $this->repository
            ->getUserService()
            ->loadUser(
                $userReference->getUserId()
            );

        $dt = new \DateTimeImmutable();

        /** @var EzInfoCollection $ezInfo */
        $ezInfo = $this->infoCollectionRepository->getInstance();

        $ezInfo->setContentObjectId($location->getContentInfo()->id);
        $ezInfo->setUserIdentifier($user->login);
        $ezInfo->setCreatorId($user->id);
        $ezInfo->setCreated($dt->getTimestamp());
        $ezInfo->setModified($dt->getTimestamp());

        try {
            $this->infoCollectionRepository->save($ezInfo);
        } catch (DBALException $e) {
            throw new ActionFailedException('database', $e->getMessage());
        }

        /**
         * @var string
         * @var \eZ\Publish\Core\FieldType\Value $value
         */
        foreach ($struct->fieldsData as $fieldDefIdentifier => $value) {
            if ($value === null) {
                continue;
            }

            $value = $this->factory->getLegacyValue($value->value, $contentType->getFieldDefinition($fieldDefIdentifier));
            $ezInfoAttribute = $this->infoCollectionAttributeRepository->getInstance();

            /* @var FieldValue $value */
            $ezInfoAttribute->setContentObjectId($location->getContentInfo()->id);
            $ezInfoAttribute->setInformationCollectionId($ezInfo->getId());
            $ezInfoAttribute->setContentClassAttributeId($value->fieldDefinitionId);
            $ezInfoAttribute->setContentObjectAttributeId($content->getField($fieldDefIdentifier)->id);
            $ezInfoAttribute->setDataInt($value->dataInt);
            $ezInfoAttribute->setDataFloat($value->dataFloat);
            $ezInfoAttribute->setDataText($value->dataText);

            try {
                $this->infoCollectionAttributeRepository->save($ezInfoAttribute);
            } catch (DBALException $e) {
                throw new ActionFailedException('database', $e->getMessage());
            }
        }
    }
}
