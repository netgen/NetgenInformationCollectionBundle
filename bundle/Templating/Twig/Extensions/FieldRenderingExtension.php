<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Templating\Twig\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FieldRenderingExtension extends AbstractExtension
{
    /**
     * @return \Twig\TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'info_collection_render_field',
                [FieldRenderingRuntime::class, 'renderField'],
                ['is_safe' => ['html']]
            ),
        ];
    }
}
