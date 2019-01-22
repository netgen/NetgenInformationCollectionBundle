<?php

namespace Netgen\Bundle\InformationCollectionBundle\API\Service;

use Symfony\Component\HttpFoundation\Request;

interface CaptchaValue
{
    /**
     * Returns information if processed
     * $request has valid captcha response
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return bool
     */
    public function isValid(Request $request);
}
