<?php

namespace Netgen\InformationCollection\Core\EmailDataProvider;

use Ibexa\Core\Helper\FieldHelper;
use Ibexa\Core\Helper\TranslationHelper;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Netgen\InformationCollection\API\Action\EmailDataProviderInterface;
use Netgen\InformationCollection\Core\Action\EmailAction;
use Twig\Environment;

abstract class AbstractProvider implements EmailDataProviderInterface
{
    /**
     * @var array
     */
    protected $configResolver;

    /**
     * @var \Ibexa\Core\Helper\TranslationHelper
     */
    protected $translationHelper;

    /**
     * @var \Ibexa\Core\Helper\FieldHelper
     */
    protected $fieldHelper;

    /**
     * @var \Twig\Environment
     */
    protected $twig;

    /**
     * EmailDataFactory constructor.
     *
     * @param array $config
     * @param \Ibexa\Core\Helper\TranslationHelper $translationHelper
     * @param \Ibexa\Core\Helper\FieldHelper $fieldHelper
     * @param \Twig\Environment $twig
     */
    public function __construct(
        ConfigResolverInterface $configResolver,
        TranslationHelper $translationHelper,
        FieldHelper $fieldHelper,
        Environment $twig
    ) {
        $this->configResolver = $configResolver;
        $this->config = $this->configResolver->getParameter('action_config', 'netgen_information_collection')[EmailAction::$defaultName];
        $this->translationHelper = $translationHelper;
        $this->fieldHelper = $fieldHelper;
        $this->twig = $twig;
    }
}
