<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Mailer;

use Netgen\InformationCollection\API\MailerInterface;
use Symfony\Component\Mailer\MailerInterface as SfMailerInterface;
use Symfony\Component\Mime\Email;
use Throwable;

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

    public function sendEmail(Email $content): void
    {
        try {
            $this->mailer->send($content);
        } catch (Throwable $t) {
        }
    }
}
