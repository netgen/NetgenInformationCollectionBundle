<?php

namespace Netgen\Bundle\InformationCollectionBundle\Mailer;

use Netgen\Bundle\InformationCollectionBundle\Exception\EmailNotSentException;

class Mailer implements MailerInterface
{
    /**
     * @var \Swift_Mailer
     */
    protected $internalMailer;

    /**
     * Mailer constructor.
     *
     * @param \Swift_Mailer $internalMailer
     */
    public function __construct(\Swift_Mailer $internalMailer)
    {
        $this->internalMailer = $internalMailer;
    }

    /**
     * @inheritdoc
     */
    public function createMessage()
    {
        return \Swift_Message::newInstance();
    }

    /**
     * @inheritdoc
     */
    public function sendMessage(\Swift_Mime_Message $message)
    {
        if (!$this->internalMailer->send($message)) {
            throw new EmailNotSentException();
        }
    }
}
