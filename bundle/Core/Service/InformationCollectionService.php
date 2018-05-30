<?php

namespace Netgen\Bundle\InformationCollectionBundle\Core\Service;

use Netgen\Bundle\InformationCollectionBundle\API\Service\InformationCollectionService as APIInformationCollectionService;
use eZ\Publish\API\Repository\Repository;
use Netgen\Bundle\InformationCollectionBundle\Core\Persistence\Gateway\DoctrineDatabase;
use Netgen\Bundle\InformationCollectionBundle\Repository\EzInfoCollectionAttributeRepository;
use Netgen\Bundle\InformationCollectionBundle\Repository\EzInfoCollectionRepository;

class InformationCollectionService implements APIInformationCollectionService
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

    public function overview($limit = 10, $offset = 0)
    {
        $objects = $this->gateway->getObjectsWithCollections($limit, $offset);

        foreach (array_keys($objects) as $i) {
            $contentId = (int)$objects[$i]['contentobject_id'];
            $firstCollection = $this->ezInfoCollectionRepository->findOneBy(
                [
                    'contentObjectId' => $contentId,
                ],
                [
                    'created' => 'ASC',
                ]
            );

            $lastCollection = $this->ezInfoCollectionRepository->findOneBy(
                [
                    'contentObjectId' => $contentId,
                ],
                [
                    'created' => 'DESC',
                ]
            );

            $count = $this->ezInfoCollectionRepository->getChildrenCount($contentId);


            $objects[$i]['first_collection'] = $firstCollection;
            $objects[$i]['last_collection'] = $lastCollection;

            $content = $this->contentService->loadContent($contentId);
            $contentType = $this->contentTypeService->loadContentType($content->contentInfo->contentTypeId);

            $objects[$i]['class_name'] = $contentType->getName();
            $objects[$i]['collections'] = $count;
        }

        return $objects;
    }

    public function overviewCount()
    {
        return $this->gateway->getContentsWithCollectionsCount();
    }

    public function collectionList($contentId, $limit, $offset = 0)
    {
        $collections = $this->ezInfoCollectionRepository->findBy(
            [
                'contentObjectId' => $contentId,
            ],
            [],
            $limit,
            $offset
        );


        $objects = [];
        foreach ($collections as $collection) {
            $d = [
                'object' => $collection,
                'user' => $this->getUser($collection->getCreatorId()),
            ];

            $objects[] = $d;
        }

        return $objects;
    }

    public function collectionListCount($contentId)
    {
        return $this->ezInfoCollectionRepository->getChildrenCount($contentId);
    }

    public function search($contentId, $searchText, $limit, $offset = 0)
    {
        $collections = $this->ezInfoCollectionAttributeRepository->search($contentId, $searchText);

        $collections = $this->ezInfoCollectionRepository->findBy(
            [
                'id' => $collections,
            ],
            [],
            $limit,
            $offset
        );

        $objects = [];
        foreach ($collections as $collection) {
            $d = [
                'object' => $collection,
                'user' => $this->getUser($collection->getCreatorId()),
            ];

            $objects[] = $d;
        }

        return $objects;
    }

    public function searchCount($contentId, $searchText)
    {
        $collections = $this->ezInfoCollectionAttributeRepository->search($contentId, $searchText);

        $collections = $this->ezInfoCollectionRepository->findBy(
            [
                'id' => $collections,
            ]
        );

        return count($collections);
    }

    public function view($collectionId)
    {
        $collection = $this->ezInfoCollectionRepository->findOneBy(['id' => $collectionId]);

        $user = $this->getUser($collection->getCreatorId());
        $content = $this->contentService->loadContent($collection->getContentObjectId());

        $contentType = $this->contentTypeService->loadContentType($content->contentInfo->contentTypeId);
        $definitionsById = $contentType->fieldDefinitionsById;

        $collections = $this->ezInfoCollectionAttributeRepository->findBy(
            [
                'informationCollectionId' => $collectionId,
            ]
        );

        $objects = [];
        foreach ($collections as $coll) {

            $d = [
                'object' => $coll,
                'field' => $definitionsById[$coll->getContentClassAttributeId()],
            ];

            $objects[] = $d;

        }

        return [
            'collection' => $collection,
            'objects' => $objects,
            'user' => $user,
            'content' => $content,
        ];
    }

    protected function getUser($userId)
    {
        return $this->repository->getUserService()->loadUser($userId);
    }

    public function deleteCollection($contentId, $collectionId)
    {
        
    }

    public function deleteCollectionFields($contentId, $collectionId, $fields)
    {
        
    }

    public function deleteCollections($contentId, array $collections)
    {
        
    }

    public function deleteCollectionByContent(array $contentIds)
    {

    }
}
