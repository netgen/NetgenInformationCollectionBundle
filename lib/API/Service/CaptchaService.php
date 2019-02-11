<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Service;

use eZ\Publish\API\Repository\Values\Content\Location;

interface CaptchaService
{
    /**
     * Returns Captcha implementation.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     *
     * @return \Netgen\InformationCollection\API\Service\CaptchaValue
     */
    public function getCaptcha(Location $location): CaptchaValue;

    /**
     * Checks if captcha is enabled for given Location.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     *
     * @return bool
     */
    public function isEnabled(Location $location): bool;

    /**
     * Returns configured captcha site key for given Location.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     *
     * @return string
     */
    public function getSiteKey(Location $location): string;
}
