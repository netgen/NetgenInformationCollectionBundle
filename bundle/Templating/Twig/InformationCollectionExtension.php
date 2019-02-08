<?php

namespace Netgen\Bundle\InformationCollectionBundle\Templating\Twig;

use eZ\Publish\API\Repository\Values\Content\Location;
use Netgen\Bundle\InformationCollectionBundle\Form\Captcha\CaptchaService;
use Twig_Extension;
use Twig_SimpleFunction;

class InformationCollectionExtension extends Twig_Extension
{
    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\Form\Captcha\CaptchaService
     */
    protected $captcha;

    /**
     * InformationCollectionExtension constructor.
     *
     * @param \Netgen\Bundle\InformationCollectionBundle\Form\Captcha\CaptchaService $captcha
     */
    public function __construct(CaptchaService $captcha)
    {
        $this->captcha = $captcha;
    }

    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('info_collection_captcha_is_enabled', [$this, 'isEnabled']),
            new Twig_SimpleFunction('info_collection_captcha_get_site_key', [$this, 'getSiteKey']),
        ];
    }

    /**
     * Checks if captcha is enabled for given Location
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     *
     * @return bool
     */
    public function isEnabled(Location $location)
    {
        return $this->captcha->isEnabled($location);
    }

    /**
     * Return configured site key for given Location
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     *
     * @return string
     */
    public function getSiteKey(Location $location)
    {
        return $this->captcha->getSiteKey($location);
    }
}
