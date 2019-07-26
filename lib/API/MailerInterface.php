<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API;

interface MailerInterface
{
    /**
     * Creates and sends email message.
     *
     * @param \Netgen\InformationCollection\API\Value\DataTransfer\EmailContent $content
     *
     * @throws \Netgen\InformationCollection\API\Exception\EmailNotSentException
     */
    public function createAndSendMessage(Value\DataTransfer\EmailContent $content): void;
}
