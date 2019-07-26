<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value\Captcha;

use Netgen\InformationCollection\API\Service\CaptchaValue;
use Symfony\Component\HttpFoundation\Request;

class NullObject implements CaptchaValue
{
    /**
     * {@inheritdoc}
     */
    public function isValid(Request $request): bool
    {
        return true;
    }
}
