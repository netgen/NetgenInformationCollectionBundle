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
    public $recipient;

    /**
     * @var string
     */
    public $subject;

    /**
     * @var string
     */
    public $sender;

    /**
     * @var string
     */
    public $body;

    /**
     * @var BinaryFile[]|null
     */
    public $attachments;

    /**
     * EmailData constructor.
     *
     * @param string $recipient
     * @param string $sender
     * @param string $subject
     * @param string $body
     * @param BinaryFile[] $attachments
     */
    public function __construct($recipient, $sender, $subject, $body, $attachments = null)
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
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return bool
     */
    public function hasAttachments()
    {
        return !empty($this->attachments);
    }

    /**
     * @return BinaryFile[]
     */
    public function getAttachments()
    {
        return $this->attachments;
    }
}
