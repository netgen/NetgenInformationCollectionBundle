<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Factory;

use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Netgen\InformationCollection\API\ConfigurationConstants;
use Netgen\InformationCollection\API\Constants;
use Netgen\InformationCollection\API\Exception\MissingValueException;
use Netgen\InformationCollection\API\Value\DataTransfer\EmailContent;
use Netgen\InformationCollection\API\Value\DataTransfer\TemplateContent;
use Netgen\InformationCollection\API\Value\Event\InformationCollected;
use function array_filter;
use function array_key_exists;
use function explode;
use function filter_var;
use function trim;
use const FILTER_VALIDATE_EMAIL;

class AutoResponderDataFactory extends EmailDataFactory
{
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
}
