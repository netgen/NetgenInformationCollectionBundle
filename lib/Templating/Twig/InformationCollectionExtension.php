<?php

namespace Netgen\InformationCollection\Templating\Twig;

use eZ\Publish\API\Repository\Values\Content\Location;
use Netgen\InformationCollection\API\Service\CaptchaService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class InformationCollectionExtension extends AbstractExtension
{
    /**
     * @var \Netgen\InformationCollection\API\Service\CaptchaService
     */
    protected $captcha;

    /**
     * InformationCollectionExtension constructor.
     *
     * @param \Netgen\InformationCollection\API\Service\CaptchaService $captcha
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
            new TwigFunction('info_collection_captcha_is_enabled', [$this, 'isEnabled']),
            new TwigFunction('info_collection_captcha_get_site_key', [$this, 'getSiteKey']),
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
