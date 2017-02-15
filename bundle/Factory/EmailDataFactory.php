<?php

namespace Netgen\Bundle\InformationCollectionBundle\Factory;

use Netgen\Bundle\InformationCollectionBundle\Value\TemplateData;
use Twig_Environment;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\Core\Helper\FieldHelper;
use eZ\Publish\Core\Helper\TranslationHelper;
use Netgen\Bundle\InformationCollectionBundle\DependencyInjection\ConfigurationConstants;
use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;
use Netgen\Bundle\InformationCollectionBundle\Value\EmailData;
use Netgen\Bundle\InformationCollectionBundle\Constants;

class EmailDataFactory
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var TranslationHelper
     */
    protected $translationHelper;

    /**
     * @var FieldHelper
     */
    protected $fieldHelper;

    /**
     * @var ContentTypeService
     */
    protected $contentTypeService;

    /**
     * @var ContentService
     */
    protected $contentService;
    /**
     * @var Twig_Environment
     */
    private $twig;

    /**
     * EmailDataFactory constructor.
     *
     * @param array $config
     * @param TranslationHelper $translationHelper
     * @param FieldHelper $fieldHelper
     * @param ContentTypeService $contentTypeService
     * @param ContentService $contentService
     * @param Twig_Environment $twig
     */
    public function __construct(
        array $config,
        TranslationHelper $translationHelper,
        FieldHelper $fieldHelper,
        ContentTypeService $contentTypeService,
        ContentService $contentService,
        Twig_Environment $twig
    ) {
    
        $this->config = $config;
        $this->translationHelper = $translationHelper;
        $this->fieldHelper = $fieldHelper;
        $this->contentTypeService = $contentTypeService;
        $this->contentService = $contentService;
        $this->twig = $twig;
    }

    /**
     * Factory method
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
            $this->resolveBody($data)
        );
    }

    /**
     * Returns resolved parameter
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
            return $data->getTemplateWrapper()->render(
                [
                    'event' => $data->getEvent(),
                    'content' => $data->getContent(),
                ]
            );
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
     * Returns resolved template name
     *
     * @param string $contentTypeIdentifier
     *
     * @return string
     */
    protected function resolveTemplate($contentTypeIdentifier)
    {
        if (array_key_exists($contentTypeIdentifier, $this->config[ConfigurationConstants::TEMPLATES])) {

            return $this->config[ConfigurationConstants::TEMPLATES][$contentTypeIdentifier];

        }

        return $this->config[ConfigurationConstants::TEMPLATES][ConfigurationConstants::SETTINGS_DEFAULT];
    }

    /**
     * Renders email template
     *
     * @param TemplateData $data
     *
     * @return string
     */
    protected function resolveBody(TemplateData $data)
    {
        return $data->getTemplateWrapper()
            ->render(
                [
                    'event' => $data->getEvent(),
                    'content' => $data->getContent(),
                ]
            );
    }
}
