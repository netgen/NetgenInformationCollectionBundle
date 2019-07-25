<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Templating\Twig;

class AdminGlobalVariable
{
    /**
     * @var string
     */
    protected $pageLayoutTemplate;

    /**
     * Sets the pagelayout template.
     *
     * @param string $pageLayoutTemplate
     */
    public function setPageLayoutTemplate($pageLayoutTemplate = null)
    {
        $this->pageLayoutTemplate = $pageLayoutTemplate;
    }

    /**
     * Returns the pagelayout template.
     *
     * @return string
     */
    public function getPageLayoutTemplate()
    {
        return $this->pageLayoutTemplate;
    }
}
