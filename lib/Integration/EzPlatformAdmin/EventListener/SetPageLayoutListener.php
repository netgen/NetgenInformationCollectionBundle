<?php

namespace Netgen\Bundle\InformationCollectionBundle\Integration\EzPlatformAdmin\EventListener;

use EzSystems\EzPlatformAdminUiBundle\EzPlatformAdminUiBundle;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Netgen\Bundle\InformationCollectionBundle\Templating\Twig\AdminGlobalVariable;

class SetPageLayoutListener implements EventSubscriberInterface
{
    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\Templating\Twig\AdminGlobalVariable
     */
    protected $globalVariable;

    /**
     * @var string
     */
    protected $pageLayoutTemplate;

    /**
     * @var array
     */
    protected $groupsBySiteAccess;

    public function __construct(
        AdminGlobalVariable $adminGlobalVariable,
        array $groupsBySiteAccess,
        $pageLayoutTemplate
    )
    {
        $this->globalVariable = $adminGlobalVariable;
        $this->pageLayoutTemplate = $pageLayoutTemplate;
        $this->groupsBySiteAccess = $groupsBySiteAccess;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $siteAccess = $event->getRequest()->attributes->get('siteaccess')->name;
        if (!isset($this->groupsBySiteAccess[$siteAccess])) {
            return;
        }

        if (!in_array(EzPlatformAdminUiBundle::ADMIN_GROUP_NAME, $this->groupsBySiteAccess[$siteAccess], true)) {
            return;
        }

        $this->globalVariable->setPageLayoutTemplate($this->pageLayoutTemplate);
    }
}