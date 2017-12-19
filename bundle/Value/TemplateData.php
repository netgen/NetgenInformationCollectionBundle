<?php

namespace Netgen\Bundle\InformationCollectionBundle\Value;

use eZ\Publish\API\Repository\Values\Content\Content;
use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;

/**
 * @property InformationCollected $event
 * @property Content $content
 * @property \Twig\TemplateWrapper $templateWrapper
 */
class TemplateData extends ValueObject
{
    /**
     * @var InformationCollected
     */
    protected $event;

    /**
     * @var Content
     */
    protected $content;

    /**
     * @var \Twig\TemplateWrapper
     */
    protected $templateWrapper;
}
