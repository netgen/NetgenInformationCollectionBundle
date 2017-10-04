<?php

namespace Netgen\Bundle\InformationCollectionBundle\Factory;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\Core\Helper\FieldHelper;
use eZ\Publish\Core\Helper\TranslationHelper;
use eZ\Publish\Core\FieldType\BinaryFile\Value as BinaryFile;
use Netgen\Bundle\InformationCollectionBundle\Constants;
use Netgen\Bundle\InformationCollectionBundle\DependencyInjection\ConfigurationConstants;
use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;
use Netgen\Bundle\InformationCollectionBundle\Exception\MissingEmailBlockException;
use Netgen\Bundle\InformationCollectionBundle\Value\EmailData;
use Netgen\Bundle\InformationCollectionBundle\Value\TemplateData;
use Twig_Environment;

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
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * EmailDataFactory constructor.
     *
     * @param array $config
     * @param \eZ\Publish\Core\Helper\TranslationHelper $translationHelper
     * @param \eZ\Publish\Core\Helper\FieldHelper $fieldHelper
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \Twig_Environment $twig
     */
    public function __construct(
        array $config,
        TranslationHelper $translationHelper,
        FieldHelper $fieldHelper,
        ContentService $contentService,
        Twig_Environment $twig
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
        $data = new TemplateData($value, $content, $templateWrapper);

        return new EmailData(
            $this->resolve($data, Constants::FIELD_RECIPIENT, Constants::FIELD_TYPE_EMAIL),
            $this->resolve($data, Constants::FIELD_SENDER, Constants::FIELD_TYPE_EMAIL),
            $this->resolve($data, Constants::FIELD_SUBJECT),
            $this->resolveBody($data),
            $this->resolveAttachments($contentType->identifier, $value->getInformationCollectionStruct()->getCollectedFields())
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
        if ($data->getTemplateWrapper()->hasBlock($field)) {
            $rendered = $data->getTemplateWrapper()->renderBlock(
                $field,
                array(
                    'event' => $data->getEvent(),
                    'collected_fields' => $data->getEvent()->getInformationCollectionStruct()->getCollectedFields(),
                    'content' => $data->getContent(),
                )
            );

            return trim($rendered);
        }

        $content = $data->getContent();
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
        if (array_key_exists($contentTypeIdentifier, $this->config[ConfigurationConstants::TEMPLATES][ConfigurationConstants::CONTENT_TYPES])) {
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
        if ($data->getTemplateWrapper()->hasBlock(Constants::BLOCK_EMAIL)) {
            return $data->getTemplateWrapper()
                ->renderBlock(
                    Constants::BLOCK_EMAIL,
                    array(
                        'event' => $data->getEvent(),
                        'collected_fields' => $data->getEvent()->getInformationCollectionStruct()->getCollectedFields(),
                        'content' => $data->getContent(),
                        'default_variables' => $this->config[ConfigurationConstants::DEFAULT_VARIABLES],
                    )
                );
        }

        throw new MissingEmailBlockException(
            $data->getTemplateWrapper()->getSourceContext()->getName(),
            $data->getTemplateWrapper()->getBlockNames()
        );
    }

    /**
     * @param $contentTypeIdentifier
     * @param array $collectedFields
     *
     * @return BinaryFile[]|null
     */
    protected function resolveAttachments($contentTypeIdentifier, array $collectedFields)
    {
        if (array_key_exists($contentTypeIdentifier, $this->config['attachments'][ConfigurationConstants::CONTENT_TYPES])) {
            $send = $this->config['attachments'][ConfigurationConstants::CONTENT_TYPES][$contentTypeIdentifier];
        } else {
            $send = $this->config['attachments']['send'];
        }

        if (!$send) {
            return null;
        }

        return $this->getBinaryFileFields($collectedFields);
    }

    /**
     * @param array $collectedFields
     *
     * @return BinaryFile[]|null
     */
    protected function getBinaryFileFields(array $collectedFields)
    {
        $filtered = [];
        foreach ($collectedFields as $identifier => $value) {
            if ($value instanceof BinaryFile) {
                $filtered[] = $value;
            }
        }

        return empty($filtered) ? null : $filtered;
    }
}
