<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Integration\EzPlatformAdmin;

use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use EzSystems\EzPlatformAdminUi\Menu\MainMenuBuilder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MenuListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            ConfigureMenuEvent::MAIN_MENU => ['onMenuConfigure', 0],
        ];
    }

    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();
        
        if (!isset($menu[MainMenuBuilder::ITEM_ADMIN])) {
            return;
        }
        
        $menu[MainMenuBuilder::ITEM_ADMIN]->addChild(
            'information_collection',
            [
                'label' => 'Information collection',
                'route' => 'netgen_information_collection.route.admin.overview',
                'extras' => [
                    'routes' => [
                        'collection_list' => 'netgen_information_collection.route.admin.collection_list',
                        'collection_list_search' => 'netgen_information_collection.route.admin.collection_list_search',
                        'view' => 'netgen_information_collection.route.admin.view',
                        'handle_contents' => 'netgen_information_collection.route.admin.handle_contents',
                        'handle_collection_list' => 'netgen_information_collection.route.admin.handle_collection_list',
                        'handle_collection' => 'netgen_information_collection.route.admin.handle_collection',
                        'tree' => 'netgen_information_collection.route.admin.tree_get_children',
                        'export' => 'netgen_information_collection.route.admin.export',
                    ],
                ],
            ]
        );
    }
}
