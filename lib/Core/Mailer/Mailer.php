<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Mailer;

use Netgen\InformationCollection\API\Exception\EmailNotSentException;
use Netgen\InformationCollection\API\Value\DataTransfer\EmailContent;
use Netgen\InformationCollection\API\MailerInterface;

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
    public function createAndSendMessage(EmailContent $data): void
    {
        $message = new \Swift_Message();

        try {
            $message->setTo($data->getRecipient());
        } catch (\Swift_RfcComplianceException $e) {
            throw new EmailNotSentException('recipient', $e->getMessage());
        }

        try {
            $message->setFrom($data->getSender());
        } catch (\Swift_RfcComplianceException $e) {
            throw new EmailNotSentException('sender', $e->getMessage());
        }

        $message->setSubject($data->getSubject());
        $message->setBody($data->getBody(), 'text/html');

        if ($data->hasAttachments()) {
            foreach ($data->getAttachments() as $attachment) {
                $message->attach(
                    \Swift_Attachment::fromPath($attachment->inputUri, $attachment->mimeType)
                        ->setFilename($attachment->fileName)
                );
            }
        }

        if (!$this->internalMailer->send($message)) {
            throw new EmailNotSentException('send', 'invalid mailer configuration?');
        }
    }
}
