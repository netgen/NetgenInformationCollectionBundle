<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Templating\Twig;

use eZ\Publish\Core\MVC\ConfigResolverInterface;

class AdminGlobalVariable
{
    /**
     * @var \eZ\Publish\Core\MVC\ConfigResolverInterface
     */
    protected $configResolver;

    /**
     * @var string|null
     */
    protected $pageLayoutTemplate;

    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    public function setPageLayoutTemplate(string $pageLayoutTemplate): void
    {
        $this->pageLayoutTemplate = $pageLayoutTemplate;
    }

    /**
     * Returns the pagelayout template.
     *
     * @return string|null
     */
    public function getPageLayoutTemplate(): ?string
    {
        if ($this->pageLayoutTemplate !== null) {
            return $this->pageLayoutTemplate;
        }

        return $this->configResolver->getParameter('admin.pagelayout', 'netgen_information_collection');
    }
}
