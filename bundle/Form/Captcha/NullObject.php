<?php

namespace Netgen\Bundle\InformationCollectionBundle\Form\Captcha;

use Symfony\Component\HttpFoundation\Request;

class NullObject implements CaptchaValueInterface
{
    public function isValid(Request $request)
    {
        return true;
    }
}
