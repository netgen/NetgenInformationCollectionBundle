<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Service;

use eZ\Publish\API\Repository\Repository;
use Netgen\InformationCollection\API\Service\InformationCollection;
use Netgen\InformationCollection\API\Value\Attribute;
use Netgen\InformationCollection\API\Value\Collection;
use Netgen\InformationCollection\API\Value\Collections;
use Netgen\InformationCollection\API\Value\Content;
use Netgen\InformationCollection\API\Value\ContentsWithCollections;
use Netgen\InformationCollection\API\Value\Filter\Query;
use Netgen\InformationCollection\Core\Persistence\Gateway\DoctrineDatabase;
use Netgen\InformationCollection\Doctrine\Entity\EzInfoCollectionAttribute;
use Netgen\InformationCollection\Doctrine\Repository\EzInfoCollectionRepository;
use Netgen\InformationCollection\Doctrine\Repository\EzInfoCollectionAttributeRepository;

class InformationCollectionService implements InformationCollection
{
    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\Repository\EzInfoCollectionRepository
     */
    protected $ezInfoCollectionRepository;

    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\Repository\EzInfoCollectionAttributeRepository
     */
    protected $ezInfoCollectionAttributeRepository;

    /**
     * @var \eZ\Publish\API\Repository\Repository
     */
    protected $repository;

    /**
     * @var \eZ\Publish\API\Repository\ContentService
     */
    protected $contentService;

    /**
     * @var \eZ\Publish\API\Repository\ContentTypeService
     */
    protected $contentTypeService;

    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\Core\Persistence\Gateway\DoctrineDatabase
     */
    protected $gateway;

    /**
     * InformationCollectionService constructor.
     *
     * @param \Netgen\Bundle\InformationCollectionBundle\Repository\EzInfoCollectionRepository $ezInfoCollectionRepository
     * @param \Netgen\Bundle\InformationCollectionBundle\Repository\EzInfoCollectionAttributeRepository $ezInfoCollectionAttributeRepository
     * @param \eZ\Publish\API\Repository\Repository $repository
     * @param \Netgen\Bundle\InformationCollectionBundle\Core\Persistence\Gateway\DoctrineDatabase $gateway
     */
    public function __construct(
        EzInfoCollectionRepository $ezInfoCollectionRepository,
        EzInfoCollectionAttributeRepository $ezInfoCollectionAttributeRepository,
        Repository $repository,
        DoctrineDatabase $gateway
    ) {
        $this->ezInfoCollectionRepository = $ezInfoCollectionRepository;
        $this->ezInfoCollectionAttributeRepository = $ezInfoCollectionAttributeRepository;
        $this->repository = $repository;
        $this->contentService = $repository->getContentService();
        $this->contentTypeService = $repository->getContentTypeService();
        $this->gateway = $gateway;
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectsWithCollections(Query $query): ContentsWithCollections
    {
        if ($query->limit === Query::COUNT_QUERY) {
            return new ContentsWithCollections([
                'count' => $this->gateway->getContentsWithCollectionsCount(),
            ]);
        }

        $objects = $this->gateway->getObjectsWithCollections($query->limit, $query->offset);

        $contents = [];
        foreach ($objects as $object) {
            $content = $this->contentService->loadContent((int) $object['content_id']);

            $firstCollection = $this->ezInfoCollectionRepository->findOneBy(
                [
                    'contentObjectId' => $content->id,
                ],
                [
                    'created' => 'ASC',
                ]
            );

            $lastCollection = $this->ezInfoCollectionRepository->findOneBy(
                [
                    'contentObjectId' => $content->id,
                ],
                [
                    'created' => 'DESC',
                ]
            );

            $contents[] = new Content(
                [
                    'content' => $content,
                    'contentType' => $this->contentTypeService->loadContentType($content->contentInfo->contentTypeId),
                    'firstCollection' => $firstCollection,
                    'lastCollection' => $lastCollection,
                    'count' => $this->ezInfoCollectionRepository->getChildrenCount($content->id),
                    'hasLocation' => empty($object['main_node_id']) ? false : true,
                ]
            );
        }

        return new ContentsWithCollections(
            [
                'contents' => $contents,
                'count' => count($contents),
            ]
        );
    }

    public function getCollections(Query $query): Collections
    {
        if ($query->limit === Query::COUNT_QUERY) {
            return new Collections([
                'count' => $this->ezInfoCollectionRepository->getChildrenCount($query->contentId),
                'collections' => [],
            ]);
        }

        $collections = $this->ezInfoCollectionRepository->findBy(
            [
                'contentObjectId' => $query->contentId,
            ],
            [],
            $query->limit,
            $query->offset
        );

        $objects = [];
        foreach ($collections as $collection) {
            $objects[] = $this->loadCollection($collection->getId());
        }

        return new Collections(
            [
                'collections' => $objects,
                'count' => count($objects),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function search(Query $query): Collections
    {
        if ($query->limit === Query::COUNT_QUERY) {
            $collections = $this->ezInfoCollectionAttributeRepository->search($query->contentId, $query->searchText);

            // needs rewrite
            $collections = $this->ezInfoCollectionRepository->findBy(
                [
                    'id' => $collections,
                ]
            );

            return new Collections([
                'count' => count($collections),
                'collections' => [],
            ]);
        }

        $collections = $this->ezInfoCollectionAttributeRepository->search($query->contentId, $query->searchText);

        $collections = $this->ezInfoCollectionRepository->findBy(
            [
                'id' => $collections,
            ],
            [],
            $query->limit,
            $query->offset
        );

        $objects = [];
        foreach ($collections as $collection) {
            $objects[] = $this->loadCollection($collection->getId());
        }

        return new Collections(
            [
                'collections' => $objects,
                'count' => count($objects),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection(Query $query): Collection
    {
        return $this->loadCollection($query->collectionId);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteCollectionFields(Query $query): void
    {
        $attributes = $this->ezInfoCollectionAttributeRepository
            ->findBy(
                [
                    'contentObjectId' => $query->contentId,
                    'informationCollectionId' => $query->collectionId,
                    'contentClassAttributeId' => $query->fields,
                ]);

        $this->ezInfoCollectionAttributeRepository->remove($attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteCollections(Query $query): void
    {
        $collections = $this->ezInfoCollectionRepository
            ->findBy([
                'contentObjectId' => $query->contentId,
                'id' => $query->collections,
            ]);

        foreach ($collections as $collection) {
            $attributes = $this->ezInfoCollectionAttributeRepository->findBy(['informationCollectionId' => $collection->getId()]);
            $this->ezInfoCollectionAttributeRepository->remove($attributes);
        }

        $this->ezInfoCollectionRepository->remove($collections);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteCollectionByContent(Query $query): void
    {
        $collections = $this->ezInfoCollectionRepository
            ->findBy([
                'contentObjectId' => $query->contents,
            ]);

        foreach ($collections as $collection) {
            $attributes = $this->ezInfoCollectionAttributeRepository->findBy(['informationCollectionId' => $collection->getId()]);
            $this->ezInfoCollectionAttributeRepository->remove($attributes);
        }

        $this->ezInfoCollectionRepository->remove($collections);
    }

    /**
     * @param int $userId
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     *
     * @return \eZ\Publish\API\Repository\Values\User\User
     */
    protected function getUser($userId)
    {
        return $this->repository->getUserService()->loadUser($userId);
    }

    /**
     * @param int $collectionId
     *
     * @return \Netgen\Bundle\InformationCollectionBundle\API\Value\InformationCollection\Collection
     */
    protected function loadCollection($collectionId)
    {
        $collection = $this->ezInfoCollectionRepository->findOneBy(['id' => $collectionId]);

        $content = $this->contentService->loadContent($collection->getContentObjectId());

        $contentType = $this->contentTypeService->loadContentType($content->contentInfo->contentTypeId);
        $definitionsById = $contentType->fieldDefinitionsById;

        $collections = $this->ezInfoCollectionAttributeRepository->findBy(
            [
                'informationCollectionId' => $collectionId,
            ]
        );

        $attributes = [];
        /** @var EzInfoCollectionAttribute $coll */
        foreach ($collections as $coll) {
            if (empty($definitionsById[$coll->getContentClassAttributeId()])) {
                continue;
            }

            $attributes[] = new Attribute([
                'entity' => $coll,
                'field' => $definitionsById[$coll->getContentClassAttributeId()],
            ]);
        }

        return new Collection([
            'entity' => $collection,
            'attributes' => $attributes,
            'user' => $this->getUser($collection->getCreatorId()),
            'content' => $content,
        ]);
    }
}
