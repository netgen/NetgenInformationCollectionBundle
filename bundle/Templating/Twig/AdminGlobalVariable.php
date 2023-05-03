<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Templating\Twig;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;

class AdminGlobalVariable
{
    protected ConfigResolverInterface $configResolver;

    protected ?string $pageLayoutTemplate;

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
     */
    public function getPageLayoutTemplate(): ?string
    {
        if ($this->pageLayoutTemplate !== null) {
            return $this->pageLayoutTemplate;
        }

        return $this->configResolver->getParameter('admin.pagelayout', 'netgen_information_collection');
    }
}
