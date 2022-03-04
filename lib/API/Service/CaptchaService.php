<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Service;

use Ibexa\Contracts\Core\Repository\Values\Content\Location;

interface CaptchaService
{
    /**
     * Returns Captcha implementation.
     *
     * @return \Netgen\InformationCollection\API\Service\CaptchaValue
     */
    public function getCaptcha(Location $location): CaptchaValue;

    /**
     * Checks if captcha is enabled for given Location.
     */
    public function isEnabled(Location $location): bool;

    /**
     * Returns configured captcha site key for given Location.
     */
    public function getSiteKey(Location $location): string;

    /**
     * Returns the captcha configuration for given Location.
     *
     * @return string
     */
    public function getConfig(Location $location): array;
}
