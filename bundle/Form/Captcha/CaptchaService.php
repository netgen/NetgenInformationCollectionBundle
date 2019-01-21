<?php

namespace Netgen\Bundle\InformationCollectionBundle\Form\Captcha;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;

class CaptchaService
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var \eZ\Publish\API\Repository\ContentTypeService
     */
    protected $contentTypeService;

    public function __construct(ContentTypeService $contentTypeService, $config = [])
    {
        $this->config = $config;
        $this->contentTypeService = $contentTypeService;
    }

    /**
     * @return \Netgen\Bundle\InformationCollectionBundle\Form\Captcha\CaptchaValueInterface
     */
    public function getCaptcha(Location $location)
    {
        if ($this->canCaptchaBeEnabled($location)) {
            return $this->processConfiguration();
        }

        return new NullObject();
    }

    protected function processConfiguration()
    {
        $reCaptcha = new \ReCaptcha\ReCaptcha($this->config['secret']);

        if (!empty($this->config['options'])) {


            if (!empty($this->config['options']['hostname'])) {
                $reCaptcha->setExpectedHostname($this->config['options']['hostname']);
            }
            if (!empty($this->config['options']['apk_package_name'])) {
                $reCaptcha->setExpectedApkPackageName($this->config['options']['apk_package_name']);
            }
            if (!empty($this->config['options']['action'])) {
                $reCaptcha->setExpectedAction($this->config['options']['action']);
            }
            if (!empty($this->config['options']['score_threshold'])) {
                $reCaptcha->setScoreThreshold($this->config['options']['score_threshold']);
            }
            if (!empty($this->config['options']['challenge_timeout'])) {
                $reCaptcha->setChallengeTimeout($this->config['options']['challenge_timeout']);
            }
        }

        return new ReCaptcha($reCaptcha);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     *
     * @return bool
     */
    public function canCaptchaBeEnabled(Location $location)
    {
        $contentType = $this->contentTypeService
            ->loadContentType($location->contentInfo->contentTypeId);

        if (!empty($this->config['override_by_type'])) {
            if (in_array($contentType->identifier, array_keys($this->config['override_by_type']))) {
                return $this->config['override_by_type'][$contentType->identifier];
            }
        }

        return $this->config['enabled'];
    }
}
