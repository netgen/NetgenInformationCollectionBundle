<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Templating\Twig\Extensions;

use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Netgen\InformationCollection\API\Service\CaptchaService;

class CaptchaRuntime
{
    /**
     * @var \Netgen\InformationCollection\API\Service\CaptchaService
     */
    protected $captcha;

    public function __construct(CaptchaService $captcha)
    {
        $this->captcha = $captcha;
    }

    /**
     * Checks if captcha is enabled for given Location.
     */
    public function isEnabled(Location $location): bool
    {
        return $this->captcha->isEnabled($location);
    }

    /**
     * Return configured site key for given Location.
     */
    public function getSiteKey(Location $location): string
    {
        return $this->captcha->getSiteKey($location);
    }
}
