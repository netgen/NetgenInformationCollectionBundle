<?php

namespace Netgen\Bundle\InformationCollectionBundle\Controller\Admin;

use Ibexa\Core\MVC\Symfony\Security\Authorization\Attribute;
use Ibexa\Bundle\Core\Controller;
use Netgen\InformationCollection\API\Service\InformationCollection;
use Netgen\InformationCollection\API\Value\Content;
use Netgen\InformationCollection\API\Value\Filter\ContentId;
use Netgen\InformationCollection\API\Value\Filter\Query;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class TreeController extends Controller
{
    protected TranslatorInterface $translator;

    protected RouterInterface $router;

    protected InformationCollection $service;

    public function __construct(
        InformationCollection $service,
        TranslatorInterface $translator,
        RouterInterface $router
    ) {
        $this->translator = $translator;
        $this->router = $router;
        $this->service = $service;
    }

    /**
     * Get contents with collections
     *
     * @param bool $isRoot
     */
    public function getChildrenAction($isRoot = false): JsonResponse
    {
        $isRoot = (bool) $isRoot;
        $attribute = new Attribute('infocollector', 'read');
        $this->denyAccessUnlessGranted($attribute);

        $result = array();

        if ($isRoot) {
            $result[] = $this->getRootTreeData();
        } else {

            $query = Query::withLimit($this->getConfigResolver()->getParameter('admin.tree_limit', 'netgen_information_collection'));

            $objects = $this->service->getObjectsWithCollections($query);
            foreach ($objects->getContents() as $content) {
                $result[] = $this->getCollections($content, $isRoot);
            }
        }

        return (new JsonResponse())->setData($result);
    }

    /**
     * Generates data for root of the tree.
     */
    protected function getRootTreeData(): array
    {
        $count = $this->service->getObjectsWithCollectionsCount();

        return array(
            'id' => '0',
            'parent' => '#',
            'text' => $this->translator->trans('netgen_information_collection_admin_collected_information', ['%count%' => $count->getCount()], 'netgen_information_collection_admin'),
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
     * @param bool $isRoot
     */
    protected function getCollections(Content $content, $isRoot = false): array
    {
        $isRoot = (bool) $isRoot;
        $languages = $this->getConfigResolver()->getParameter('languages');

        $query = ContentId::countWithContentId($content->getContent()->id);

        $count = $this->service->getCollectionsCount($query);

        return array(
            'id' => $content->getContent()->id,
            'parent' => $isRoot ? '#' : '0',
            'text' => $content->getContent()->getName(in_array($languages[0], $content->getContent()->getVersionInfo()->languageCodes) ? $languages[0] : null) . ' (' . strval($count->getCount()) . ')',
            'children' => false,
            'a_attr' => array(
                'href' => $this->router->generate('netgen_information_collection.route.admin.collection_list', ['contentId' => $content->getContent()->id]),
                'rel' => $content->getContent()->id,
            ),
            'state' => array(
                'opened' => $isRoot,
            ),
            'data' => array(
                'context_menu' => array(
                    array(
                        'name' => 'export',
                        'url' => $this->router->generate('netgen_information_collection.route.admin.export', ['contentId' => $content->getContent()->id]),
                        'text' => $this->translator->trans('netgen_information_collection_admin_export_export', [], 'netgen_information_collection_admin'),
                    ),
                ),
            ),
        );
    }
}
