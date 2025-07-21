<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Service;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Netgen\InformationCollection\API\Service\CaptchaService as CaptchaServiceInterface;
use Netgen\InformationCollection\API\Service\CaptchaValue;
use Netgen\InformationCollection\API\Value\Captcha\NullObject;
use Netgen\InformationCollection\API\Value\Captcha\ReCaptcha;
use function array_keys;
use function array_replace;
use function in_array;

class CaptchaService implements CaptchaServiceInterface
{
    protected array $config;

    protected ContentTypeService $contentTypeService;

    private ConfigResolverInterface $configResolver;

    public function __construct(ContentTypeService $contentTypeService, ConfigResolverInterface $configResolver)
    {
        $this->config = $configResolver->getParameter('captcha', 'netgen_information_collection');
        $this->contentTypeService = $contentTypeService;
        $this->configResolver = $configResolver;
    }

    public function isEnabled(Location $location): bool
    {
        $config = $this->getConfig($location);

        return $config['enabled'];
    }

    public function getSiteKey(Location $location): string
    {
        $config = $this->getConfig($location);

        return $config['site_key'];
    }

    public function getCaptcha(Location $location): CaptchaValue
    {
        $config = $this->getConfig($location);

        if ($config['enabled']) {
            $reCaptcha = new \ReCaptcha\ReCaptcha($config['secret']);

            if (!empty($config['options'])) {
//                if (!empty($config['options']['hostname'])) {
                $reCaptcha->setExpectedHostname('localhost');
//                }
//                if (!empty($config['options']['apk_package_name'])) {
//                    $reCaptcha->setExpectedApkPackageName($config['options']['apk_package_name']);
//                }
                if (!empty($config['options']['action'])) {
                    $reCaptcha->setExpectedAction($config['options']['action']);
                }
//                if (!empty($config['options']['score_threshold'])) {
//                    $reCaptcha->setScoreThreshold($config['options']['score_threshold']);
//                }
//                if (!empty($config['options']['challenge_timeout'])) {
//                    $reCaptcha->setChallengeTimeout($config['options']['challenge_timeout']);
//                }
            }

            return new ReCaptcha($reCaptcha);
        }

        return new NullObject();
    }

    /**
     * Returns filtered config for current Location.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function getConfig(Location $location): array
    {
        $contentTypeConfig = $this->getConfigForContentType(
            $this->getContentType($location)
        );

        return array_replace($this->config, $contentTypeConfig);
    }

    /**
     * Returns filtered config for current ContentType.
     */
    protected function getConfigForContentType(ContentType $contentType): array
    {
        if ($this->hasConfigForContentType($contentType)) {
            return $this->config['override_by_type'][$contentType->identifier];
        }

        return [];
    }

    /**
     * Checks if override exist for given ContentType.
     */
    protected function hasConfigForContentType(ContentType $contentType): bool
    {
        if (!empty($this->config['override_by_type'])) {
            if (in_array($contentType->identifier, array_keys($this->config['override_by_type']), true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Helper method for retrieving ContentType from Location.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    protected function getContentType(Location $location): ContentType
    {
        return $this->contentTypeService
            ->loadContentType($location->contentInfo->contentTypeId);
    }
}
