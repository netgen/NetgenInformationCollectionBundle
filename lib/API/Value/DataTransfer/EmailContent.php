<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value\DataTransfer;

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
     * @var \Ibexa\Core\FieldType\BinaryFile\Value[]
     */
    protected $attachments = [];

    /**
     * @param \Ibexa\Core\FieldType\BinaryFile\Value[] $attachments
     */
    public function __construct(array $recipients, array $sender, string $subject, string $body, array $attachments = [])
    {
        $this->recipients = $recipients;
        $this->subject = $subject;
        $this->sender = $sender;
        $this->body = $body;
        $this->attachments = $attachments;
    }

    public function getRecipients(): array
    {
        return $this->recipients;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getSender(): array
    {
        return $this->sender;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function hasAttachments(): bool
    {
        return !empty($this->attachments);
    }

    /**
     * @return \Ibexa\Core\FieldType\BinaryFile\Value[]
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }
}
