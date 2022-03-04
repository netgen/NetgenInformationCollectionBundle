<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value\DataTransfer;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
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

    public function __construct(InformationCollected $event, TemplateWrapper $templateWrapper)
    {
        $this->event = $event;
        $this->templateWrapper = $templateWrapper;
    }

    public function getEvent(): InformationCollected
    {
        return $this->event;
    }

    public function getContent(): Content
    {
        return $this->event->getContent();
    }

    public function getTemplateWrapper(): TemplateWrapper
    {
        return $this->templateWrapper;
    }
}
