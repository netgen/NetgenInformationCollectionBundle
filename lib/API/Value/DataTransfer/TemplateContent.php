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
     * @var \Twig\TemplateWrapper
     */
    protected $templateWrapper;

    /**
     * TemplateData constructor.
     *
     * @param \Netgen\InformationCollection\API\Value\Event\InformationCollected $event
     * @param \Twig\TemplateWrapper $templateWrapper
     */
    public function __construct(InformationCollected $event, TemplateWrapper $templateWrapper)
    {
        $this->event = $event;
        $this->templateWrapper = $templateWrapper;
    }

    /**
     * @return \Netgen\InformationCollection\API\Value\Event\InformationCollected
     */
    public function getEvent(): InformationCollected
    {
        return $this->event;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Content
     */
    public function getContent(): Content
    {
        return $this->event->getContent();
    }

    /**
     * @return \Twig\TemplateWrapper
     */
    public function getTemplateWrapper(): TemplateWrapper
    {
        return $this->templateWrapper;
    }
}
