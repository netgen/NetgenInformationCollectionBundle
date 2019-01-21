<?php

namespace Netgen\Bundle\InformationCollectionBundle\Form\Captcha;

class CaptchaService
{
    /**
     * @var array
     */
    protected $config;

    public function __construct($config = [])
    {
        $this->config = $config;
    }

    /**
     * @return \Netgen\Bundle\InformationCollectionBundle\Form\Captcha\CaptchaValueInterface
     */
    public function getCaptcha()
    {
        if ($this->config['enabled']) {
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
}
