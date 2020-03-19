<?php

namespace Netgen\Bundle\InformationCollectionBundle\Templating\Twig;

use eZ\Publish\Core\MVC\ConfigResolverInterface;

class AdminGlobalVariable
{
    /**
     * @var \eZ\Publish\Core\MVC\ConfigResolverInterface
     */
    protected $configResolver;

    /**
     * AdminGlobalVariable constructor.
     *
     * @param \eZ\Publish\Core\MVC\ConfigResolverInterface $configResolver
     */
    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    /**
     * Returns the pagelayout template.
     *
     * @return string
     */
    public function getPageLayoutTemplate()
    {
        return $this->configResolver->getParameter('admin.pagelayout', 'netgen_information_collection');
    }
}
