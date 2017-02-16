<?php

namespace Netgen\Bundle\InformationCollectionBundle\Mailer;

use Netgen\Bundle\InformationCollectionBundle\Value\EmailData;

interface MailerInterface
{
    /**
     * Creates and sends email message
     *
     * @param EmailData $data
     *
     * @throws \Netgen\Bundle\InformationCollectionBundle\Exception\EmailNotSentException
     */
    public function createAndSendMessage(EmailData $data);
}
