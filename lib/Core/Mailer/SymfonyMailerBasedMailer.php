<?php

namespace Netgen\InformationCollection\Core\Mailer;

use Netgen\InformationCollection\API\MailerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface as SfMailerInterface;
use Symfony\Component\Mime\Email;

class SymfonyMailerBasedMailer implements MailerInterface
{
    /**
     * @var \Symfony\Component\Mailer\MailerInterface
     */
    private $mailer;

    public function __construct(SfMailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendEmail(Email $content): void
    {
        $this->mailer->send($content);
    }
}
