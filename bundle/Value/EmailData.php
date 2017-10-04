<?php

namespace Netgen\Bundle\InformationCollectionBundle\Value;

use eZ\Publish\Core\FieldType\BinaryFile\Value as BinaryFile;

class EmailData
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
