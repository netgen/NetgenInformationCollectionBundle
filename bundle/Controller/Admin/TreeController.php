<?php

namespace Netgen\Bundle\InformationCollectionBundle\Controller\Admin;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use Netgen\Bundle\InformationCollectionBundle\API\InformationCollectionService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class TreeController extends Controller
{
    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    protected $translator;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    protected $router;

    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\API\InformationCollectionService
     */
    private $service;

    /**
     * TreeController constructor.
     *
     * @param \Netgen\Bundle\InformationCollectionBundle\API\InformationCollectionService
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     * @param \Symfony\Component\Routing\RouterInterface $router
     */
    public function __construct(
        InformationCollectionService $service,
        TranslatorInterface $translator,
        RouterInterface $router
    ) {
        $this->translator = $translator;
        $this->router = $router;
        $this->service = $service;
    }

    public function getChildrenAction($isRoot = false)
    {
        $this->denyAccessUnlessGranted('ez:infocollector:read');

        $result = array();

        if ((bool) $isRoot) {
            $result[] = $this->getRootTreeData();
        } else {

            $data = $this->service->overview(10);
            foreach ($data as $datum) {
                $result[] = $this->getCollections($datum, $isRoot);
            }
        }

        return (new JsonResponse())->setData($result);
    }

    /**
     * Generates data for root of the tree.
     *
     * @return array
     */
    protected function getRootTreeData()
    {
        $count = $this->service->overviewCount();

        return array(
            'id' => '0',
            'parent' => '#',
            'text' => 'Collected information' . ' (' . strval($count) . ')',
            'children' => true,
            'state' => array(
                'opened' => true,
            ),
            'a_attr' => array(
                'href' => $this->router->generate('netgen_information_collection.route.admin.overview'),
                'rel' => '0',
            ),
            'data' => array(
            ),
        );
    }


    protected function getCollections($data, $isRoot = false)
    {
        $count = $this->service->collectionListCount($data['contentobject_id']);

        return array(
            'id' => $data['contentobject_id'],
            'parent' => $isRoot ? '#' : '0',
            'text' => $data['class_name'] . ' (' . strval($count) . ')',
            'children' => false,
            'a_attr' => array(
                'href' => $this->router->generate('netgen_information_collection.route.admin.collection_list', ['contentId' => $data['contentobject_id']]),
                'rel' => $data['contentobject_id'],
            ),
            'state' => array(
                'opened' => $isRoot,
            ),
            'data' => array(
                'context_menu' => array(
                    array(
                        'name' => 'delete',
                        'url' => '',
                        'text' => 'Delete',
                    ),
                    array(
                        'name' => 'anonymize',
                        'url' => '',
                        'text' => "Anonymize",
                    ),
                ),
            ),
        );
    }
}