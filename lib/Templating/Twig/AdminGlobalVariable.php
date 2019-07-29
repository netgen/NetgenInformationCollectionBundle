<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Templating\Twig;

class AdminGlobalVariable
{
    /**
     * @var string|null
     */
    protected $pageLayoutTemplate;

    /**
     * Sets the pagelayout template.
     *
     * @param string $pageLayoutTemplate
     */
    public function setPageLayoutTemplate(?string $pageLayoutTemplate = null): void
    {
        $this->pageLayoutTemplate = $pageLayoutTemplate;
    }

    /**
     * Returns the pagelayout template.
     *
     * @return string
     */
    public function getPageLayoutTemplate(): ?string
    {
        return $this->pageLayoutTemplate;
    }
}
