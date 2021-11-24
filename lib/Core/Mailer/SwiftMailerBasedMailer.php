<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Mailer;

use Netgen\InformationCollection\API\Exception\EmailNotSentException;
use Netgen\InformationCollection\API\MailerInterface;
use Swift_Message;
use Symfony\Component\Mime\Email;

class SwiftMailerBasedMailer implements MailerInterface
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
    public function sendEmail(Email $content): void
    {
        $message = new Swift_Message();

        try {
            $message->setTo(
                array_map(
                    function(\Symfony\Component\Mime\Address $address) {
                        return $address->getAddress();
                    },
                    $content->getTo()
                )
            );
        } catch (\Swift_RfcComplianceException $e) {
            throw new EmailNotSentException('recipients', $e->getMessage());
        }

        if (!$content->getFrom())
        {
            throw new EmailNotSentException('sender', "No sender has been set for the email.");
        }

        $message->setFrom(
            array_map(
                function(\Symfony\Component\Mime\Address $address) {
                    return $address->getAddress();
                },
                $content->getFrom()
            )
        );

        $message->setSubject($content->getSubject());

        // attachments still todo

        if (!empty($content->getAttachments())) {
            foreach ($content->getAttachments() as $attachment) {
                $body = $attachment->getBody();
                $preparedHeaders = $attachment->getPreparedHeaders();

                $filename = $preparedHeaders->get('content-disposition')->getParameter('filename');
                $contentType = $preparedHeaders->get('content-type')->getBody();

                $message->attach(
                    new \Swift_Attachment($body, $filename, $contentType)
                );

            }
        }

        $message->setBody(
            $content->getHtmlBody(),
            'text/html'
        );

        if (!$this->internalMailer->send($message)) {
            throw new EmailNotSentException('send', 'invalid mailer configuration?');
        }
    }
}
