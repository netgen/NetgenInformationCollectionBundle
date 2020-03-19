<?php

namespace Netgen\Bundle\InformationCollectionBundle\Templating\Twig;

use eZ\Publish\Core\MVC\ConfigResolverInterface;

class AdminGlobalVariable
{
    /**
     * @var string
     */
    protected $pageLayoutTemplate;

    /**
     * @var \eZ\Publish\Core\MVC\ConfigResolverInterface
     */
    protected $configResolver;

    /**
     * @var bool
     */
    protected $isDefault = true;

    /**
     * AdminGlobalVariable constructor.
     *
     * @param \eZ\Publish\Core\MVC\ConfigResolverInterface $configResolver
     */
    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
        $this->pageLayoutTemplate = $configResolver->getParameter('admin.pagelayout', 'netgen_information_collection');
    }

    public function setPageLayoutTemplate($pageLayoutTemplate)
    {
        $this->pageLayoutTemplate = $pageLayoutTemplate;
        $this->isDefault = false;
    }

    /**
     * Returns the pagelayout template.
     *
     * @return string
     */
    public function getPageLayoutTemplate()
    {
        if ($this->isDefault) {
            $this->pageLayoutTemplate = $this->configResolver->getParameter('admin.pagelayout', 'netgen_information_collection');
        }

        return $this->pageLayoutTemplate;
    }
}
