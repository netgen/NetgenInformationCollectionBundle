<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value\DataTransfer;

use Ibexa\Core\FieldType\BinaryFile\Value as BinaryFile;
use Netgen\InformationCollection\API\Value\ValueObject;

class EmailContent extends ValueObject
{
    /**
     * @var array
     */
    protected $recipients;

    /**
     * @var string
     */
    protected $subject;

    /**
     * @var array
     */
    protected $sender;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var BinaryFile[]
     */
    protected $attachments = [];

    /**
     * EmailData constructor.
     *
     * @param array $recipients
     * @param array $sender
     * @param string $subject
     * @param string $body
     * @param BinaryFile[] $attachments
     */
    public function __construct(array $recipients, array $sender, string $subject, string $body, array $attachments = [])
    {
        $this->recipients = $recipients;
        $this->subject = $subject;
        $this->sender = $sender;
        $this->body = $body;
        $this->attachments = $attachments;
    }

    /**
     * @return array
     */
    public function getRecipients(): array
    {
        return $this->recipients;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return array
     */
    public function getSender(): array
    {
        return $this->sender;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @return bool
     */
    public function hasAttachments(): bool
    {
        return !empty($this->attachments);
    }

    /**
     * @return BinaryFile[]
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }
}
