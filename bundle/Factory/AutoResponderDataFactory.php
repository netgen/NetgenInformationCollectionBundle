<?php

namespace Netgen\Bundle\InformationCollectionBundle\Factory;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\Core\Helper\FieldHelper;
use eZ\Publish\Core\Helper\TranslationHelper;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Netgen\Bundle\InformationCollectionBundle\Constants;
use Netgen\Bundle\InformationCollectionBundle\DependencyInjection\ConfigurationConstants;
use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;
use Netgen\Bundle\InformationCollectionBundle\Exception\MissingValueException;
use Netgen\Bundle\InformationCollectionBundle\Value\EmailData;
use Netgen\Bundle\InformationCollectionBundle\Value\TemplateData;
use Twig_Environment;
use function array_key_exists;
use function trim;

class AutoResponderDataFactory extends EmailDataFactory
{
    /**
     * EmailDataFactory constructor.
     *
     * @param \eZ\Publish\Core\MVC\ConfigResolverInterface $configResolver
     * @param \eZ\Publish\Core\Helper\TranslationHelper $translationHelper
     * @param \eZ\Publish\Core\Helper\FieldHelper $fieldHelper
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \Twig_Environment $twig
     */
    public function __construct(
        ConfigResolverInterface $configResolver,
        TranslationHelper $translationHelper,
        FieldHelper $fieldHelper,
        ContentService $contentService,
        Twig_Environment $twig
    ) {
        $this->translationHelper = $translationHelper;
        $this->fieldHelper = $fieldHelper;
        $this->contentService = $contentService;
        $this->twig = $twig;
        $this->configResolver = $configResolver;
        $this->config = $this->configResolver->getParameter('action_config.auto_responder', 'netgen_information_collection');
    }

    /**
     * Factory method.
     *
     * @param InformationCollected $value
     *
     * @return EmailData
     */
    public function build(InformationCollected $value)
    {
        $location = $value->getLocation();
        $contentType = $value->getContentType();
        $content = $this->contentService->loadContent($location->contentId);

        $template = $this->resolveTemplate($contentType->identifier);

        $templateWrapper = $this->twig->load($template);
        $data = new TemplateData($value, $content, $templateWrapper);

        $body = $this->resolveBody($data);

        return new EmailData(
            $this->resolveRecipient($data),
            $this->resolve($data, Constants::FIELD_SENDER, Constants::FIELD_TYPE_EMAIL),
            $this->resolveSubject($data),
            $body
        );
    }

    /**
     * Returns resolved parameter.
     *
     * @param TemplateData $data
     *
     * @return string
     */
    protected function resolveRecipient(TemplateData $data)
    {
        $fields = $data->getEvent()->getInformationCollectionStruct()->getCollectedFields();
        if ($data->getTemplateWrapper()->hasBlock(Constants::FIELD_RECIPIENT)) {
            $rendered = $data->getTemplateWrapper()->renderBlock(
                Constants::FIELD_RECIPIENT,
                array(
                    'event' => $data->getEvent(),
                    'collected_fields' => $fields,
                    'content' => $data->getContent(),
                    'default_variables' => !empty($this->config[ConfigurationConstants::DEFAULT_VARIABLES])
                        ? $this->config[ConfigurationConstants::DEFAULT_VARIABLES] : null,
                )
            );

            return trim($rendered);
        }

        $field = 'email';
        if (!empty($this->config[ConfigurationConstants::DEFAULT_VARIABLES][ConfigurationConstants::EMAIL_FIELD_IDENTIFIER])) {
            $field = $this->config[ConfigurationConstants::DEFAULT_VARIABLES][ConfigurationConstants::EMAIL_FIELD_IDENTIFIER];
        }

        if (array_key_exists($field, $fields)) {
            return $fields[$field]->email;
        }

        throw new MissingValueException($field);
    }

    /**
     * Returns resolved parameter.
     *
     * @param TemplateData $data
     *
     * @return string
     */
    protected function resolveSubject(TemplateData $data)
    {
        $fields = $data->getEvent()->getInformationCollectionStruct()->getCollectedFields();
        if ($data->getTemplateWrapper()->hasBlock(Constants::FIELD_AUTO_RESPONDER_SUBJECT)) {
            $rendered = $data->getTemplateWrapper()->renderBlock(
                Constants::FIELD_AUTO_RESPONDER_SUBJECT,
                array(
                    'event' => $data->getEvent(),
                    'collected_fields' => $fields,
                    'content' => $data->getContent(),
                    'default_variables' => !empty($this->config[ConfigurationConstants::DEFAULT_VARIABLES])
                        ? $this->config[ConfigurationConstants::DEFAULT_VARIABLES] : null,
                )
            );

            return trim($rendered);
        }

        $content = $data->getContent();
        if (array_key_exists(Constants::FIELD_AUTO_RESPONDER_SUBJECT, $content->fields) &&
            !$this->fieldHelper->isFieldEmpty($content, Constants::FIELD_AUTO_RESPONDER_SUBJECT)
        ) {
            $fieldValue = $this->translationHelper->getTranslatedField($content, Constants::FIELD_AUTO_RESPONDER_SUBJECT);

            return $fieldValue->value->text;
        }

        if (!empty($this->config[ConfigurationConstants::DEFAULT_VARIABLES][ConfigurationConstants::EMAIL_SUBJECT])) {
            return $this->config[ConfigurationConstants::DEFAULT_VARIABLES][ConfigurationConstants::EMAIL_SUBJECT];
        }

        $message = Constants::FIELD_AUTO_RESPONDER_SUBJECT . "|" .ConfigurationConstants::EMAIL_SUBJECT;
        throw new MissingValueException($message);
    }
}
