<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Templating\Twig\Extensions;

use eZ\Publish\API\Repository\Values\Content\Location;
use Netgen\InformationCollection\API\Service\CaptchaService;

class CaptchaRuntime
{
    /**
     * @var \Netgen\InformationCollection\API\Service\CaptchaService
     */
    protected $captcha;

    /**
     * CaptchaRuntime constructor.
     *
     * @param \Netgen\InformationCollection\API\Service\CaptchaService $captcha
     */
    public function __construct(CaptchaService $captcha)
    {
        $this->captcha = $captcha;
    }

    /**
     * Checks if captcha is enabled for given Location.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     *
     * @return bool
     */
    public function isEnabled(Location $location): bool
    {
        return $this->captcha->isEnabled($location);
    }

    /**
     * Return configured site key for given Location.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     *
     * @return string
     */
    public function getSiteKey(Location $location): string
    {
        return $this->captcha->getSiteKey($location);
    }
}
