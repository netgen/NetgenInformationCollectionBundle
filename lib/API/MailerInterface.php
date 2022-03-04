<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API;

use Symfony\Component\Mime\Email;

interface MailerInterface
{
    /**
     * Sends email message.
     *
     * @throws \Netgen\InformationCollection\API\Exception\EmailNotSentException
     */
    public function sendEmail(Email $content): void;
}
