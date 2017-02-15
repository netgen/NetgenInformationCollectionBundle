<?php

namespace Netgen\Bundle\InformationCollectionBundle\Value;

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
     * EmailData constructor.
     *
     * @param string $recipient
     * @param string $sender
     * @param string $subject
     * @param string $body
     */
    public function __construct($recipient, $sender, $subject, $body)
    {
        $this->recipient = $recipient;
        $this->subject = $subject;
        $this->sender = $sender;
        $this->body = $body;
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
}
