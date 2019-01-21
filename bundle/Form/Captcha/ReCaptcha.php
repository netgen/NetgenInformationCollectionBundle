<?php

namespace Netgen\Bundle\InformationCollectionBundle\Form\Captcha;

use Symfony\Component\HttpFoundation\Request;
use ReCaptcha\ReCaptcha as BaseReCaptcha;

class ReCaptcha implements CaptchaValueInterface
{
    /**
     * @var \ReCaptcha\ReCaptcha
     */
    protected $reCaptcha;

    public function __construct(BaseReCaptcha $reCaptcha)
    {
        $this->reCaptcha = $reCaptcha;
    }

    public function isValid(Request $request)
    {
        $response = $this->reCaptcha->verify(
            $request->request->get('g-recaptcha-response'), $request->getClientIp()
        );

        return $response->isSuccess();
    }

    /**
     * @return \ReCaptcha\ReCaptcha
     */
    public function getInnerCaptcha()
    {
        return $this->reCaptcha;
    }

}
