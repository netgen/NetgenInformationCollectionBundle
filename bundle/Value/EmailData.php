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
    protected $template;

    /**
     * @var string
     */
    protected $sender;

    /**
     * EmailData constructor.
     *
     * @param string $recipient
     * @param string $sender
     * @param string $subject
     * @param string $template
     */
    public function __construct($recipient, $sender, $subject, $template)
    {
        $this->recipient = $recipient;
        $this->subject = $subject;
        $this->template = $template;
        $this->sender = $sender;
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
    public function getTemplate()
    {
        return $this->template;
    }
}
