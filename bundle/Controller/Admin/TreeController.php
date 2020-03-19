<?php

namespace Netgen\Bundle\InformationCollectionBundle\Controller\Admin;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\Core\Helper\TranslationHelper;
use Netgen\Bundle\InformationCollectionBundle\API\Service\InformationCollection;
use Netgen\Bundle\InformationCollectionBundle\API\Value\InformationCollection\Content;
use Netgen\Bundle\InformationCollectionBundle\API\Value\InformationCollection\Query;
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
     * @var \Netgen\Bundle\InformationCollectionBundle\API\Service\InformationCollection
     */
    protected $service;

    /**
     * @var \eZ\Publish\Core\Helper\TranslationHelper
     */
    private $translationHelper;

    /**
     * TreeController constructor.
     *
     * @param \Netgen\Bundle\InformationCollectionBundle\API\Service\InformationCollection
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     * @param \Symfony\Component\Routing\RouterInterface $router
     * @param \eZ\Publish\Core\Helper\TranslationHelper $translationHelper
     */
    public function __construct(
        InformationCollection $service,
        TranslatorInterface $translator,
        RouterInterface $router,
        TranslationHelper $translationHelper
    ) {
        $this->translator = $translator;
        $this->router = $router;
        $this->service = $service;
        $this->translationHelper = $translationHelper;
    }

    /**
     * Get contents with collections
     *
     * @param bool $isRoot
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getChildrenAction($isRoot = false)
    {
        $this->denyAccessUnlessGranted('ez:infocollector:read');

        $result = array();

        if ((bool) $isRoot) {
            $result[] = $this->getRootTreeData();
        } else {

            $query = new Query([
                'limit' => $this->getConfigResolver()->getParameter('admin.tree_limit', 'netgen_information_collection'),
            ]);

            $objects = $this->service->getObjectsWithCollections($query);
            foreach ($objects->contents as $content) {
                $result[] = $this->getCollections($content, $isRoot);
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
        $count = $this->service->getObjectsWithCollections(Query::count());

        return array(
            'id' => '0',
            'parent' => '#',
            'text' => $this->translator->trans('netgen_information_collection_admin_collected_information', ['%count%' => $count->count], 'netgen_information_collection_admin'),
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

    /**
     * Creates tree structure for Content
     *
     * @param \Netgen\Bundle\InformationCollectionBundle\API\Value\InformationCollection\Content $content
     * @param bool $isRoot
     *
     * @return array
     */
    protected function getCollections(Content $content, $isRoot = false)
    {
        $query = new Query([
            'contentId' => $content->content->id,
            'limit' => Query::COUNT_QUERY,
        ]);

        $count = $this->service->getCollections($query);

        return array(
            'id' => $content->content->id,
            'parent' => $isRoot ? '#' : '0',
            'text' => $this->translationHelper->getTranslatedContentName($content->content) . ' (' . strval($count->count) . ')',
            'children' => false,
            'a_attr' => array(
                'href' => $this->router->generate('netgen_information_collection.route.admin.collection_list', ['contentId' => $content->content->id]),
                'rel' => $content->content->id,
            ),
            'state' => array(
                'opened' => $isRoot,
            ),
            'data' => array(
                'context_menu' => array(
                    array(
                        'name' => 'export',
                        'url' => $this->router->generate('netgen_information_collection.route.admin.export', ['contentId' => $content->content->id]),
                        'text' => $this->translator->trans('netgen_information_collection_admin_export_export', [], 'netgen_information_collection_admin'),
                    ),
                ),
            ),
        );
    }
}
