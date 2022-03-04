<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value\Captcha;

use Netgen\InformationCollection\API\Service\CaptchaValue;
use ReCaptcha\ReCaptcha as BaseReCaptcha;
use Symfony\Component\HttpFoundation\Request;
use function dump;

class ReCaptcha implements CaptchaValue
{
    protected BaseReCaptcha $reCaptcha;

    public function __construct(BaseReCaptcha $reCaptcha)
    {
        $this->reCaptcha = $reCaptcha;
    }

    public function isValid(Request $request): bool
    {
        $clientIp = $request->getClientIp() === '::1' ? 'localhost' : $request->getClientIp();

        $response = $this->reCaptcha->verify(
            $request->request->get('g-recaptcha-response'),
            $clientIp
        );

        dump($response);

        return $response->isSuccess();
    }

    /**
     * Returns aggregated captcha implementation.
     */
    public function getInnerCaptcha(): BaseReCaptcha
    {
        return $this->reCaptcha;
    }
}
