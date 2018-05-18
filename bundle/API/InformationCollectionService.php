<?php

namespace Netgen\Bundle\InformationCollectionBundle\API;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use eZ\Publish\API\Repository\Repository;
use Netgen\Bundle\InformationCollectionBundle\Entity\EzInfoCollection;
use Netgen\Bundle\InformationCollectionBundle\Entity\EzInfoCollectionAttribute;
use Netgen\Bundle\InformationCollectionBundle\Repository\EzInfoCollectionAttributeRepository;
use Netgen\Bundle\InformationCollectionBundle\Repository\EzInfoCollectionRepository;

class InformationCollectionService
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var EzInfoCollectionRepository
     */
    protected $ezInfoCollectionRepository;

    /**
     * @var EzInfoCollectionAttributeRepository
     */
    protected $ezInfoCollectionAttributeRepository;

    protected $q = <<<EOD
SELECT DISTINCT ezcontentobject.id AS contentobject_id,
	ezcontentobject.name,
	ezcontentobject_tree.main_node_id,
	ezcontentclass.serialized_name_list,
	ezcontentclass.identifier AS class_identifier
FROM ezcontentobject,
	ezcontentobject_tree,
	ezcontentclass
WHERE ezcontentobject_tree.contentobject_id = ezcontentobject.id
AND ezcontentobject.contentclass_id = ezcontentclass.id
AND ezcontentclass.version = 0
AND ezcontentobject.id IN
( SELECT DISTINCT ezinfocollection.contentobject_id FROM ezinfocollection )
ORDER BY ezcontentobject.name ASC
EOD;

    protected $overviewCount = <<<EOD
SELECT COUNT(*) as count
FROM ezcontentobject,
	ezcontentobject_tree,
	ezcontentclass
WHERE ezcontentobject_tree.contentobject_id = ezcontentobject.id
AND ezcontentobject.contentclass_id = ezcontentclass.id
AND ezcontentclass.version = 0
AND ezcontentobject.id IN
( SELECT DISTINCT ezinfocollection.contentobject_id FROM ezinfocollection )
ORDER BY ezcontentobject.name ASC;
EOD;


    protected $countQuery = <<<EOD
    SELECT COUNT( DISTINCT ezinfocollection.contentobject_id ) as count
FROM ezinfocollection,
	ezcontentobject,
	ezcontentobject_tree
WHERE ezinfocollection.contentobject_id = ezcontentobject.id
AND ezinfocollection.contentobject_id = ezcontentobject_tree.contentobject_id
EOD;
    /**
     * @var Repository
     */
    protected $repository;

    protected $contentService;

    protected $contentTypeService;


    public function __construct(EntityManagerInterface $entityManager, Repository $repository)
    {
        $this->entityManager = $entityManager;
        $this->ezInfoCollectionRepository = $this->entityManager->getRepository(EzInfoCollection::class);
        $this->ezInfoCollectionAttributeRepository = $this->entityManager->getRepository(EzInfoCollectionAttribute::class);
        $this->repository = $repository;
        $this->contentService = $repository->getContentService();
        $this->contentTypeService = $repository->getContentTypeService();
    }

    public function overview($limit = 10, $offset = 0)
    {
        $query = $this->entityManager->getConnection()->prepare($this->q);

        $query->execute();

        $objects = $query->fetchAll();

        $query = $this->entityManager->getConnection()->prepare($this->countQuery);

        $query->execute();

        $mainCount = $query->fetchColumn(0);

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
        $query = $this->entityManager->getConnection()->prepare($this->countQuery);

        $query->execute();

        $mainCount = $query->fetchColumn(0);

        return $mainCount;
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
}
