<?php

namespace Netgen\Bundle\InformationCollectionBundle\API\Service;

use eZ\Publish\API\Repository\Values\Content\Location;

interface CaptchaService
{
    /**
     * Returns Captcha implementation
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     *
     * @return \Netgen\Bundle\InformationCollectionBundle\API\Service\CaptchaValue
     */
    public function getCaptcha(Location $location);

    /**
     * Checks if captcha is enabled for given Location
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     *
     * @return bool
     */
    public function isEnabled(Location $location);

    /**
     * Returns configured captcha site key for given Location
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     *
     * @return string
     */
    public function getSiteKey(Location $location);
}
