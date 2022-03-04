<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Templating\Twig\Extensions;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Netgen\InformationCollection\API\Value\Attribute;
use Netgen\InformationCollection\API\Value\Collection;
use Twig\Environment;
use Twig\TemplateWrapper;

class FieldRenderingRuntime
{
    public const FIELD_VIEW_SUFFIX = '_field';

    protected Environment $environment;

    protected ConfigResolverInterface $configResolver;

    public function __construct(Environment $environment, ConfigResolverInterface $configResolver)
    {
        $this->environment = $environment;
        $this->configResolver = $configResolver;
    }

    public function renderField(Collection $collection, Attribute $attribute): string
    {
        $template = $this->getTemplate();
        $blockName = $this->getRenderFieldBlockName($attribute);

        if (!$template->hasBlock($blockName)) {
            return $template->renderBlock($this->getDefaultRenderFieldBlockName(), ['collection' => $collection, 'attribute' => $attribute]);
        }

        return $template->renderBlock($blockName, ['collection' => $collection, 'attribute' => $attribute]);
    }

    protected function getRenderFieldBlockName(Attribute $attribute): string
    {
        $fieldTypeIdentifier = $attribute->getFieldDefinition()->fieldTypeIdentifier;

        return $fieldTypeIdentifier . self::FIELD_VIEW_SUFFIX;
    }

    protected function getDefaultRenderFieldBlockName(): string
    {
        return 'default' . self::FIELD_VIEW_SUFFIX;
    }

    protected function getTemplate(): TemplateWrapper
    {
        return $this->environment->load('@NetgenInformationCollection/admin/content_fields.html.twig');
    }
}
