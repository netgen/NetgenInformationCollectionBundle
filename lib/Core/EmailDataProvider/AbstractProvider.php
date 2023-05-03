<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\EmailDataProvider;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\Helper\FieldHelper;
use Ibexa\Core\Helper\TranslationHelper;
use Netgen\InformationCollection\API\Action\EmailDataProviderInterface;
use Netgen\InformationCollection\Core\Action\EmailAction;
use Twig\Environment;

abstract class AbstractProvider implements EmailDataProviderInterface
{
    protected ConfigResolverInterface $configResolver;

    protected TranslationHelper $translationHelper;

    protected FieldHelper $fieldHelper;

    protected Environment $twig;

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
