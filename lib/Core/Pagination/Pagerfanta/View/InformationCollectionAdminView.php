<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Pagination\Pagerfanta\View;

use Closure;
use Pagerfanta\PagerfantaInterface;
use Pagerfanta\View\ViewInterface;
use Twig\Environment;
use function max;
use function min;
use function trim;

class InformationCollectionAdminView implements ViewInterface
{
    protected Environment $twig;

    protected string $template;

    protected PagerfantaInterface $pagerfanta;

    protected Closure $routeGenerator;

    protected int $proximity;

    protected int $startPage;

    protected int $endPage;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Sets the default template.
     */
    public function setDefaultTemplate(string $template): void
    {
        $this->template = $template;
    }

    /**
     * Returns the canonical name.
     */
    public function getName(): string
    {
        return 'netgen_information_collection_admin';
    }

    /**
     * Renders a Pagerfanta.
     *
     * The route generator can be any callable to generate
     * the routes receiving the page number as first and
     * unique argument.
     *
     * @param \Closure $routeGenerator A callable to generate the routes
     */
    public function render(PagerfantaInterface $pagerfanta, $routeGenerator, array $options = []): string
    {
        $this->pagerfanta = $pagerfanta;
        $this->routeGenerator = $routeGenerator;

        $this->initializeProximity($options);
        $this->calculateStartAndEndPage();

        return $this->twig->render(
            $options['template'] ?? $this->template,
            [
                'pager' => $pagerfanta,
                'pages' => $this->getPages(),
            ]
        );
    }

    /**
     * Initializes the proximity.
     */
    protected function initializeProximity(array $options): void
    {
        $this->proximity = isset($options['proximity']) ?
            (int) $options['proximity'] :
            2;
    }

    /**
     * Calculates start and end page that will be shown in the middle of pager.
     */
    protected function calculateStartAndEndPage(): void
    {
        $currentPage = $this->pagerfanta->getCurrentPage();
        $nbPages = $this->pagerfanta->getNbPages();

        $startPage = $currentPage - $this->proximity;
        $endPage = $currentPage + $this->proximity;

        if ($startPage < 1) {
            $endPage = $this->calculateEndPageForStartPageUnderflow($startPage, $endPage, $nbPages);
            $startPage = 1;
        }

        if ($endPage > $nbPages) {
            $startPage = $this->calculateStartPageForEndPageOverflow($startPage, $endPage, $nbPages);
            $endPage = $nbPages;
        }

        $this->startPage = (int) $startPage;
        $this->endPage = (int) $endPage;
    }

    /**
     * Calculates the end page when start page is underflowed.
     */
    protected function calculateEndPageForStartPageUnderflow(int $startPage, int $endPage, int $nbPages): int
    {
        return min($endPage + (1 - $startPage), $nbPages);
    }

    /**
     * Calculates the start page when end page is overflowed.
     */
    protected function calculateStartPageForEndPageOverflow(int $startPage, int $endPage, int $nbPages): int
    {
        return max($startPage - ($endPage - $nbPages), 1);
    }

    /**
     * Returns the list of all pages that need to be displayed.
     */
    protected function getPages(): array
    {
        $pages = [];

        $pages['previous_page'] = $this->pagerfanta->hasPreviousPage() ?
            $this->generateUrl($this->pagerfanta->getPreviousPage()) :
            false;

        $pages['first_page'] = $this->startPage > 1 ? $this->generateUrl(1) : false;
        $pages['mobile_first_page'] = $this->pagerfanta->getCurrentPage() > 2 ? $this->generateUrl(1) : false;

        $pages['second_page'] = $this->startPage === 3 ? $this->generateUrl(2) : false;

        $pages['separator_before'] = $this->startPage > 3 ? true : false;

        $middlePages = [];
        for ($i = $this->startPage, $end = $this->endPage; $i <= $end; ++$i) {
            $middlePages[$i] = $this->generateUrl($i);
        }

        $pages['middle_pages'] = $middlePages;

        $pages['separator_after'] = $this->endPage < $this->pagerfanta->getNbPages() - 2 ? true : false;

        $pages['second_to_last_page'] = $this->endPage === $this->pagerfanta->getNbPages() - 2 ?
            $this->generateUrl($this->pagerfanta->getNbPages() - 1) :
            false;

        $pages['last_page'] = $this->pagerfanta->getNbPages() > $this->endPage ?
            $this->generateUrl($this->pagerfanta->getNbPages()) :
            false;

        $pages['mobile_last_page'] = $this->pagerfanta->getCurrentPage() < $this->pagerfanta->getNbPages() - 1 ?
            $this->generateUrl($this->pagerfanta->getNbPages()) :
            false;

        $pages['next_page'] = $this->pagerfanta->hasNextPage() ?
            $this->generateUrl($this->pagerfanta->getNextPage()) :
            false;

        return $pages;
    }

    /**
     * Generates the URL based on provided page.
     */
    protected function generateUrl(int $page): string
    {
        $routeGenerator = $this->routeGenerator;

        // We use trim here because Pagerfanta (or Symfony?) adds an extra '?'
        // at the end of page when there are no other query params
        return trim($routeGenerator($page), '?');
    }
}
