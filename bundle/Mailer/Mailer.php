<?php

namespace Netgen\Bundle\InformationCollectionBundle\Mailer;

use Netgen\Bundle\InformationCollectionBundle\Exception\EmailNotSentException;
use Netgen\Bundle\InformationCollectionBundle\Value\EmailData;

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
     * {@inheritdoc}
     */
    public function createAndSendMessage(EmailData $data)
    {
        $message = new \Swift_Message();

        try {
            $message->setTo($data->recipient);
        } catch (\Swift_RfcComplianceException $e) {
            throw new EmailNotSentException('recipient', $e->getMessage());
        }

        try {
            $message->setFrom($data->sender);
        } catch (\Swift_RfcComplianceException $e) {
            throw new EmailNotSentException('sender', $e->getMessage());
        }

        $message->setSubject($data->subject);
        $message->setBody($data->body, 'text/html');

        if (!$this->internalMailer->send($message)) {
            throw new EmailNotSentException('send', 'invalid mailer configuration?');
        }
    }
}
