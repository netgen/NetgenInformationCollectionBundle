<?php

namespace Netgen\Bundle\InformationCollectionBundle\Templating\Twig;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Netgen\Bundle\InformationCollectionBundle\Form\Captcha\CaptchaService;
use Twig_Extension;
use Twig_Function;

class InformationCollectionExtension extends Twig_Extension
{
    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\Form\Captcha\CaptchaService
     */
    protected $captcha;

    /**
     * @var \eZ\Publish\Core\MVC\ConfigResolverInterface
     */
    protected $configResolver;

    public function __construct(CaptchaService $captcha, ConfigResolverInterface $configResolver)
    {
        $this->captcha = $captcha;
        $this->configResolver = $configResolver;
    }

    public function getFunctions()
    {
        return [
            new Twig_Function('info_collection_captcha_is_enabled', [$this, 'isEnabled']),
            new Twig_Function('info_collection_captcha_get_site_key', [$this, 'getSiteKey']),
        ];
    }

    /**
     * @param Location $location
     *
     * @return bool
     */
    public function isEnabled(Location $location)
    {
        return $this->captcha->canCaptchaBeEnabled($location);
    }

    /**
     * @return string
     */
    public function getSiteKey()
    {
        return $this->configResolver->getParameter('captcha.site_key', 'netgen_information_collection');
    }
}
