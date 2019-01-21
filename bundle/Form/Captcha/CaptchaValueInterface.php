<?php

namespace Netgen\Bundle\InformationCollectionBundle\Form\Captcha;

use Symfony\Component\HttpFoundation\Request;

interface CaptchaValueInterface
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return bool
     */
    public function isValid(Request $request);
}
