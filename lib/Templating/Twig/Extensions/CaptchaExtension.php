<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Templating\Twig\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CaptchaExtension extends AbstractExtension
{
    /**
     * @return \Twig\TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'info_collection_captcha_is_enabled',
                [CaptchaRuntime::class, 'isEnabled']
            ),
            new TwigFunction(
                'info_collection_captcha_get_site_key',
                [CaptchaRuntime::class, 'getSiteKey']
            ),
        ];
    }
}
