<?php

namespace Netgen\Bundle\InformationCollectionBundle\Core\Service;

use Netgen\Bundle\InformationCollectionBundle\API\Service\InformationCollection;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use Netgen\Bundle\InformationCollectionBundle\API\Value\InformationCollection\Attribute;
use Netgen\Bundle\InformationCollectionBundle\API\Value\InformationCollection\Collection;
use Netgen\Bundle\InformationCollectionBundle\API\Value\InformationCollection\Collections;
use Netgen\Bundle\InformationCollectionBundle\API\Value\InformationCollection\Content;
use Netgen\Bundle\InformationCollectionBundle\API\Value\InformationCollection\ContentsWithCollections;
use Netgen\Bundle\InformationCollectionBundle\API\Value\InformationCollection\Query;
use Netgen\Bundle\InformationCollectionBundle\Core\Persistence\Gateway\DoctrineDatabase;
use Netgen\Bundle\InformationCollectionBundle\Entity\EzInfoCollectionAttribute;
use Netgen\Bundle\InformationCollectionBundle\Repository\EzInfoCollectionAttributeRepository;
use Netgen\Bundle\InformationCollectionBundle\Repository\EzInfoCollectionRepository;

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
    )
    {
        $this->ezInfoCollectionRepository = $ezInfoCollectionRepository;
        $this->ezInfoCollectionAttributeRepository = $ezInfoCollectionAttributeRepository;
        $this->repository = $repository;
        $this->contentService = $repository->getContentService();
        $this->contentTypeService = $repository->getContentTypeService();
        $this->gateway = $gateway;
    }

    /**
     * @inheritdoc
     */
    public function getObjectsWithCollections(Query $query)
    {
        if ($query->limit === Query::COUNT_QUERY) {

            return new ContentsWithCollections([
                'count' => $this->gateway->getContentsWithCollectionsCount(),
            ]);
        }

        $objects = $this->gateway->getObjectsWithCollections($query->limit, $query->offset);

        $contents = [];
        foreach ($objects as $object) {

            $content = $this->contentService->loadContent((int)$object['content_id']);

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

    public function getCollections(Query $query)
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
     * @inheritdoc
     */
    public function search(Query $query)
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
     * @inheritdoc
     */
    public function getCollection(Query $query)
    {
        return $this->loadCollection($query->collectionId);
    }

    /**
     * @inheritdoc
     */
    public function deleteCollectionFields(Query $query)
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
     * @inheritdoc
     */
    public function deleteCollections(Query $query)
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
     * @inheritdoc
     */
    public function deleteCollectionByContent(Query $query)
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
     * @return \eZ\Publish\API\Repository\Values\User\User|null
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    protected function getUser($userId)
    {
        try {
            return $this->repository->getUserService()->loadUser($userId);
        } catch (NotFoundException $e) {
            return null;
        }
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
