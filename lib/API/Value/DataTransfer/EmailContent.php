<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value\DataTransfer;

use eZ\Publish\Core\FieldType\BinaryFile\Value as BinaryFile;
use Netgen\InformationCollection\API\Value\ValueObject;

class EmailContent extends ValueObject
{
    /**
     * @var string
     */
    protected $recipient;

    /**
     * @var string
     */
    protected $subject;

    /**
     * @var string
     */
    protected $sender;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var BinaryFile[]|null
     */
    protected $attachments;

    /**
     * EmailData constructor.
     *
     * @param string $recipient
     * @param string $sender
     * @param string $subject
     * @param string $body
     * @param BinaryFile[] $attachments
     */
    public function __construct(string $recipient, string $sender, string $subject, string $body, ?array $attachments = null)
    {
        $this->recipient = $recipient;
        $this->subject = $subject;
        $this->sender = $sender;
        $this->body = $body;
        $this->attachments = $attachments;
    }

    /**
     * @return string
     */
    public function getRecipient(): string
    {
        return $this->recipient;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getSender(): string
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
    public function getAttachments(): ?array
    {
        return $this->attachments;
    }
}
