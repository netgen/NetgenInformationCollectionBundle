<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Factory;

use Ibexa\Contracts\Core\Repository\FieldTypeService;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\FieldType\BinaryFile\Value as BinaryFileValue;
use Ibexa\Core\Helper\FieldHelper;
use Ibexa\Core\Helper\TranslationHelper;
use Netgen\InformationCollection\API\ConfigurationConstants;
use Netgen\InformationCollection\API\Constants;
use Netgen\InformationCollection\API\Exception\MissingEmailBlockException;
use Netgen\InformationCollection\API\Exception\MissingValueException;
use Netgen\InformationCollection\API\Factory\EmailContentFactoryInterface;
use Netgen\InformationCollection\API\Value\DataTransfer\EmailContent;
use Netgen\InformationCollection\API\Value\DataTransfer\TemplateContent;
use Netgen\InformationCollection\API\Value\Event\InformationCollected;
use Netgen\InformationCollection\Core\Action\AutoResponderAction;
use Twig\Environment;
use function array_filter;
use function array_key_exists;
use function explode;
use function filter_var;
use function trim;
use const FILTER_VALIDATE_EMAIL;

class AutoResponderDataFactory implements EmailContentFactoryInterface
{
    protected ConfigResolverInterface $configResolver;
    protected FieldTypeService $fieldTypeService;
    protected TranslationHelper $translationHelper;
    protected FieldHelper $fieldHelper;
    protected Environment $twig;
    protected $config;

    public function __construct(
        ConfigResolverInterface $configResolver,
        FieldTypeService $fieldTypeService,
        TranslationHelper $translationHelper,
        FieldHelper $fieldHelper,
        Environment $twig
    ) {
        $this->configResolver = $configResolver;
        $this->fieldTypeService = $fieldTypeService;
        $this->config = $this->configResolver->getParameter('action_config', 'netgen_information_collection')[AutoResponderAction::$defaultName];
        $this->translationHelper = $translationHelper;
        $this->fieldHelper = $fieldHelper;
        $this->twig = $twig;
    }

    /**
     * Factory method.
     */
    public function build(InformationCollected $value): EmailContent
    {
        $location = $value->getLocation();
        $contentType = $value->getContentType();

        $template = $this->resolveTemplate($contentType->identifier);

        $templateWrapper = $this->twig->load($template);
        $data = new TemplateContent($value, $templateWrapper);

        $body = $this->resolveBody($data);

        return new EmailContent(
            $this->resolveRecipient($data),
            [$this->resolve($data, Constants::FIELD_SENDER, Constants::FIELD_TYPE_EMAIL)],
            $this->resolveSubject($data),
            $body
        );
    }

    protected function resolve(TemplateContent $data, string $field, string $property = Constants::FIELD_TYPE_TEXT): string
    {
        $rendered = '';
        if ($data->getTemplateWrapper()->hasBlock($field)) {
            $rendered = $data->getTemplateWrapper()->renderBlock(
                $field,
                [
                    'event' => $data->getEvent(),
                    'collected_fields' => $data->getEvent()->getInformationCollectionStruct()->getCollectedFields(),
                    'content' => $data->getContent(),
                ]
            );

            $rendered = trim($rendered);
        }

        if (!empty($rendered)) {
            return $rendered;
        }

        $content = $data->getContent();
        if (array_key_exists($field, $content->fields)
            && !$this->fieldHelper->isFieldEmpty($content, $field)
        ) {
            $fieldValue = $this->translationHelper->getTranslatedField($content, $field);

            if ($fieldValue instanceof Field) {
                return $fieldValue->value->{$property};
            }
        }

        if (!empty($this->config[ConfigurationConstants::DEFAULT_VARIABLES][$field])) {
            return $this->config[ConfigurationConstants::DEFAULT_VARIABLES][$field];
        }

        throw new MissingValueException($field);
    }

    /**
     * Returns resolved parameter.
     */
    protected function resolveRecipient(TemplateContent $data): array
    {
        $fields = $data->getEvent()->getInformationCollectionStruct()->getCollectedFields();
        if ($data->getTemplateWrapper()->hasBlock(Constants::FIELD_RECIPIENT)) {
            $rendered = $data->getTemplateWrapper()->renderBlock(
                Constants::FIELD_RECIPIENT,
                [
                    'event' => $data->getEvent(),
                    'collected_fields' => $fields,
                    'content' => $data->getContent(),
                ]
            );

            $rendered = trim($rendered);
        }

        if (!empty($rendered)) {
            $emails = explode(',', $rendered);

            $emails = array_filter($emails, static fn ($var) => filter_var($var, FILTER_VALIDATE_EMAIL));

            if (!empty($emails)) {
                return $emails;
            }
        }

        $field = 'email';
        if (!empty($this->config[ConfigurationConstants::DEFAULT_VARIABLES][ConfigurationConstants::EMAIL_FIELD_IDENTIFIER])) {
            $field = $this->config[ConfigurationConstants::DEFAULT_VARIABLES][ConfigurationConstants::EMAIL_FIELD_IDENTIFIER];
        }

        if (array_key_exists($field, $fields)) {
            return [$fields[$field]->email];
        }

        throw new MissingValueException($field);
    }

    /**
     * Returns resolved parameter.
     */
    protected function resolveSubject(TemplateContent $data): string
    {
        $fields = $data->getEvent()->getInformationCollectionStruct()->getCollectedFields();
        if ($data->getTemplateWrapper()->hasBlock(Constants::FIELD_AUTO_RESPONDER_SUBJECT)) {
            $rendered = $data->getTemplateWrapper()->renderBlock(
                Constants::FIELD_AUTO_RESPONDER_SUBJECT,
                [
                    'event' => $data->getEvent(),
                    'collected_fields' => $fields,
                    'content' => $data->getContent(),
                ]
            );

            return trim($rendered);
        }

        $content = $data->getContent();
        if (array_key_exists(Constants::FIELD_AUTO_RESPONDER_SUBJECT, $content->fields)
            && !$this->fieldHelper->isFieldEmpty($content, Constants::FIELD_AUTO_RESPONDER_SUBJECT)
        ) {
            $fieldValue = $this->translationHelper->getTranslatedField($content, Constants::FIELD_AUTO_RESPONDER_SUBJECT);

            if ($fieldValue instanceof Field) {
                return $fieldValue->value->text;
            }
        }

        if (!empty($this->config[ConfigurationConstants::DEFAULT_VARIABLES][ConfigurationConstants::EMAIL_SUBJECT])) {
            return $this->config[ConfigurationConstants::DEFAULT_VARIABLES][ConfigurationConstants::EMAIL_SUBJECT];
        }

        $message = Constants::FIELD_AUTO_RESPONDER_SUBJECT . '|' . ConfigurationConstants::EMAIL_SUBJECT;

        throw new MissingValueException($message);
    }

    protected function resolveBody(TemplateContent $data): string
    {
        if ($data->getTemplateWrapper()->hasBlock(Constants::BLOCK_EMAIL)) {
            return $data->getTemplateWrapper()
                ->renderBlock(
                    Constants::BLOCK_EMAIL,
                    [
                        'event' => $data->getEvent(),
                        'collected_fields' => $data->getEvent()->getInformationCollectionStruct()->getFieldsData(),
                        'content' => $data->getContent(),
                        'default_variables' => !empty($this->config[ConfigurationConstants::DEFAULT_VARIABLES])
                            ? $this->config[ConfigurationConstants::DEFAULT_VARIABLES] : null,
                    ]
                );
        }

        throw new MissingEmailBlockException(
            $data->getTemplateWrapper()->getSourceContext()->getName(),
            $data->getTemplateWrapper()->getBlockNames()
        );
    }

    /**
     * @return \Ibexa\Core\FieldType\BinaryFile\Value[]
     */
    protected function resolveAttachments(string $contentTypeIdentifier, array $collectedFields): array
    {
        if (empty($this->config[ConfigurationConstants::ATTACHMENTS])) {
            return [];
        }

        $send = $this->config[ConfigurationConstants::ATTACHMENTS][ConfigurationConstants::CONTENT_TYPES][$contentTypeIdentifier] ?? true;

        if (!$send) {
            return [];
        }

        return $this->getBinaryFileFields($collectedFields);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     *
     * @return \Ibexa\Core\FieldType\BinaryFile\Value[]
     */
    protected function getBinaryFileFields(array $collectedFields): array
    {
        $filtered = [];

        foreach ($collectedFields as $fieldData) {
            /** @var \Ibexa\Contracts\ContentForms\Data\Content\FieldData $fieldData */
            $value = $fieldData->value;
            $fieldType = $this->fieldTypeService->getFieldType($fieldData->getFieldTypeIdentifier());

            if ($value instanceof BinaryFileValue && !$fieldType->isEmptyValue($value)) {
                $filtered[] = $value;
            }
        }

        return empty($filtered) ? [] : $filtered;
    }

    protected function resolveTemplate(string $contentTypeIdentifier): string
    {
        if (array_key_exists($contentTypeIdentifier, $this->config[ConfigurationConstants::TEMPLATES][ConfigurationConstants::CONTENT_TYPES])) {
            return $this->config[ConfigurationConstants::TEMPLATES][ConfigurationConstants::CONTENT_TYPES][$contentTypeIdentifier];
        }

        return $this->config[ConfigurationConstants::TEMPLATES][ConfigurationConstants::SETTINGS_DEFAULT];
    }
}
