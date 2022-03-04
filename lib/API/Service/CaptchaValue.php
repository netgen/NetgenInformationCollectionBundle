<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Service;

use Symfony\Component\HttpFoundation\Request;

interface CaptchaValue
{
    /**
     * Returns information if processed
     * $request has valid captcha response.
     */
    public function isValid(Request $request): bool;
}
