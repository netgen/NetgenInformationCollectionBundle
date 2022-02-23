<?php

namespace Netgen\InformationCollection\Core\EmailDataProvider;

use Netgen\InformationCollection\API\Action\EmailDataProviderInterface;
use Netgen\InformationCollection\API\Value\Event\InformationCollected;
use Symfony\Component\Mime\Email;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Netgen\InformationCollection\Core\Action\EmailAction;
use function array_key_exists;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Core\FieldType\BinaryFile\Value as BinaryFile;
use Ibexa\Core\Helper\FieldHelper;
use Ibexa\Core\Helper\TranslationHelper;
use Netgen\InformationCollection\API\Value\DataTransfer\EmailContent;
use Netgen\InformationCollection\API\Constants;
use Netgen\InformationCollection\API\ConfigurationConstants;
use Netgen\InformationCollection\API\Exception\MissingEmailBlockException;
use Netgen\InformationCollection\API\Exception\MissingValueException;
use Netgen\InformationCollection\API\Value\DataTransfer\TemplateContent;
use function trim;
use Twig\Environment;

class DefaultProvider implements EmailDataProviderInterface
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

    /**
     * Factory method.
     *
     * @param InformationCollected $value
     *
     * @return EmailContent
     */
    public function build(InformationCollected $value): EmailContent
    {
        $contentType = $value->getContentType();

        $template = $this->resolveTemplate($contentType->identifier);

        $templateWrapper = $this->twig->load($template);
        $data = new TemplateContent($value, $templateWrapper);

        $body = $this->resolveBody($data);

        return new EmailContent(
            $this->resolveEmail($data, Constants::FIELD_RECIPIENT),
            $this->resolveEmail($data, Constants::FIELD_SENDER),
            $this->resolve($data, Constants::FIELD_SUBJECT),
            $body,
            $this->resolveAttachments($contentType->identifier, $value->getInformationCollectionStruct()->getFieldsData())
        );
    }

    public function provide(InformationCollected $value): Email
    {
        return new Email();
    }
}
