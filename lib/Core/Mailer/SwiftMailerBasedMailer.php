<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Mailer;

use Netgen\InformationCollection\API\Exception\EmailNotSentException;
use Netgen\InformationCollection\API\MailerInterface;
use Symfony\Component\Mime\Email;

class SwiftMailerBasedMailer implements MailerInterface
{
    /**
     * @var \Swift_Mailer
     */
    protected $internalMailer;

    public function __construct(\Swift_Mailer $internalMailer)
    {
        $this->internalMailer = $internalMailer;
    }

    public function sendEmail(Email $content): void
    {
        $message = new \Swift_Message();

        try {
            $message->setTo($content->getFrom());
        } catch (\Swift_RfcComplianceException $e) {
            throw new EmailNotSentException('recipients', $e->getMessage());
        }

        try {
            $message->setFrom($data->getSender());
        } catch (\Swift_RfcComplianceException $e) {
            throw new EmailNotSentException('sender', $e->getMessage());
        }

        $message->setSubject($data->getSubject());

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
