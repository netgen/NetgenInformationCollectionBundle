<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Mailer;

interface MailerInterface
{
    /**
     * Creates and sends email message.
     *
     * @param \Netgen\InformationCollection\API\Value\DataTransfer\EmailContent $content
     *
     * @throws \Netgen\InformationCollection\API\Exception\EmailNotSentException
     */
    public function createAndSendMessage(\Netgen\InformationCollection\API\Value\DataTransfer\EmailContent $content): void;
}
