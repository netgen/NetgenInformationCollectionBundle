<?php

namespace Netgen\Bundle\InformationCollectionBundle\Form\Captcha;

use Netgen\Bundle\InformationCollectionBundle\API\Service\CaptchaValue;
use Symfony\Component\HttpFoundation\Request;

class NullObject implements CaptchaValue
{
    /**
     * @inheritdoc
     */
    public function isValid(Request $request)
    {
        return true;
    }
}
