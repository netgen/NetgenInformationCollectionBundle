<?php

namespace Netgen\Bundle\InformationCollectionBundle\Factory;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\Core\Helper\FieldHelper;
use eZ\Publish\Core\Helper\TranslationHelper;
use Netgen\Bundle\InformationCollectionBundle\Constants;
use Netgen\Bundle\InformationCollectionBundle\DependencyInjection\ConfigurationConstants;
use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;
use Netgen\Bundle\InformationCollectionBundle\Exception\MissingEmailBlockException;
use Netgen\Bundle\InformationCollectionBundle\Value\EmailData;
use Netgen\Bundle\InformationCollectionBundle\Value\TemplateData;
use Twig\Environment;

class EmailDataFactory
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var \eZ\Publish\Core\Helper\TranslationHelper
     */
    protected $translationHelper;

    /**
     * @var \eZ\Publish\Core\Helper\FieldHelper
     */
    protected $fieldHelper;

    /**
     * @var \eZ\Publish\API\Repository\ContentService
     */
    protected $contentService;

    /**
     * @var \Twig\Environment
     */
    protected $twig;

    /**
     * EmailDataFactory constructor.
     *
     * @param array $config
     * @param \eZ\Publish\Core\Helper\TranslationHelper $translationHelper
     * @param \eZ\Publish\Core\Helper\FieldHelper $fieldHelper
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \Twig\Environment $twig
     */
    public function __construct(
        array $config,
        TranslationHelper $translationHelper,
        FieldHelper $fieldHelper,
        ContentService $contentService,
        Environment $twig
    ) {
        $this->config = $config;
        $this->translationHelper = $translationHelper;
        $this->fieldHelper = $fieldHelper;
        $this->contentService = $contentService;
        $this->twig = $twig;
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
        $data = new TemplateData(array(
            'event' => $value,
            'content' => $content,
            'templateWrapper' => $templateWrapper,
        ));

        $body = $this->resolveBody($data);

        return new EmailData(
            array(
                'recipient' => $this->resolve($data, Constants::FIELD_RECIPIENT, Constants::FIELD_TYPE_EMAIL),
                'sender' => $this->resolve($data, Constants::FIELD_SENDER, Constants::FIELD_TYPE_EMAIL),
                'subject' => $this->resolve($data, Constants::FIELD_SUBJECT),
                'body' => $body,
            )
        );
    }

    /**
     * Returns resolved parameter.
     *
     * @param TemplateData $data
     * @param string $field
     * @param string $property
     *
     * @return string
     */
    protected function resolve(TemplateData $data, $field, $property = Constants::FIELD_TYPE_TEXT)
    {
        if ($data->templateWrapper->hasBlock($field)) {
            $rendered = $data->templateWrapper->renderBlock(
                $field,
                array(
                    'event' => $data->event,
                    'collected_fields' => $data->event->getInformationCollectionStruct()->getCollectedFields(),
                    'content' => $data->content,
                )
            );

            return trim($rendered);
        }

        $content = $data->content;
        if (array_key_exists($field, $content->fields) &&
            !$this->fieldHelper->isFieldEmpty($content, $field)
        ) {
            $fieldValue = $this->translationHelper->getTranslatedField($content, $field);

            return $fieldValue->value->$property;
        }

        return $this->config[ConfigurationConstants::DEFAULT_VARIABLES][$field];
    }

    /**
     * Returns resolved template name.
     *
     * @param string $contentTypeIdentifier
     *
     * @return string
     */
    protected function resolveTemplate($contentTypeIdentifier)
    {
        if (array_key_exists(
            $contentTypeIdentifier,
            $this->config[ConfigurationConstants::TEMPLATES][ConfigurationConstants::CONTENT_TYPES]
        )) {
            return $this->config[ConfigurationConstants::TEMPLATES][ConfigurationConstants::CONTENT_TYPES][$contentTypeIdentifier];
        }

        return $this->config[ConfigurationConstants::TEMPLATES][ConfigurationConstants::SETTINGS_DEFAULT];
    }

    /**
     * Renders email template.
     *
     * @param TemplateData $data
     *
     * @throws MissingEmailBlockException
     *
     * @return string
     */
    protected function resolveBody(TemplateData $data)
    {
        $templateWrapper = $data->templateWrapper;
        if ($templateWrapper->hasBlock(Constants::BLOCK_EMAIL)) {
            return $templateWrapper
                ->renderBlock(
                    Constants::BLOCK_EMAIL,
                    array(
                        'event' => $data->event,
                        'collected_fields' => $data->event->getInformationCollectionStruct()->getCollectedFields(),
                        'content' => $data->content,
                        'default_variables' => $this->config[ConfigurationConstants::DEFAULT_VARIABLES],
                    )
                );
        }

        throw new MissingEmailBlockException(
            $templateWrapper->getSourceContext()->getName(),
            $templateWrapper->getBlockNames()
        );
    }
}
