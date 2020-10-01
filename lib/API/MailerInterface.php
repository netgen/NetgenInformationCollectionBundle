<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API;

use Symfony\Component\Mime\Email;

interface MailerInterface
{
    /**
     * Sends email message.
     *
     * @param \Symfony\Component\Mime\Email $content
     *
     * @throws \Netgen\InformationCollection\API\Exception\EmailNotSentException
     */
    public function sendEmail(Email $content): void;
}
