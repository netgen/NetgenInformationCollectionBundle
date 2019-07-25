<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value\DataTransfer;

use eZ\Publish\API\Repository\Values\Content\Content;
use Netgen\InformationCollection\API\Value\Event\InformationCollected;
use Netgen\InformationCollection\API\Value\ValueObject;
use Twig\TemplateWrapper;

class TemplateContent extends ValueObject
{
    /**
     * @var \Netgen\InformationCollection\API\Value\Event\InformationCollected
     */
    protected $event;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Content
     */
    protected $content;

    /**
     * @var \Twig\TemplateWrapper
     */
    protected $templateWrapper;

    /**
     * TemplateData constructor.
     *
     * @param \Netgen\InformationCollection\API\Value\Event\InformationCollected $event
     * @param \eZ\Publish\API\Repository\Values\Content\Content $content
     * @param \Twig\TemplateWrapper $templateWrapper
     */
    public function __construct(InformationCollected $event, Content $content, TemplateWrapper $templateWrapper)
    {
        $this->event = $event;
        $this->content = $content;
        $this->templateWrapper = $templateWrapper;
    }

    /**
     * @return \Netgen\InformationCollection\API\Value\Event\InformationCollected
     */
    public function getEvent(): \Netgen\InformationCollection\API\Value\Event\InformationCollected
    {
        return $this->event;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Content
     */
    public function getContent(): \eZ\Publish\API\Repository\Values\Content\Content
    {
        return $this->content;
    }

    /**
     * @return \Twig\TemplateWrapper
     */
    public function getTemplateWrapper(): \Twig\TemplateWrapper
    {
        return $this->templateWrapper;
    }
}
