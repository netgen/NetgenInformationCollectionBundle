<?php

namespace Netgen\Bundle\InformationCollectionBundle\Mailer;

interface MailerInterface
{
    /**
     * Creates and sends email message.
     *
     * @param \Netgen\Bundle\InformationCollectionBundle\Value\EmailData $data
     *
     * @throws \Netgen\Bundle\InformationCollectionBundle\Exception\EmailNotSentException
     */
    public function createAndSendMessage(\Netgen\Bundle\InformationCollectionBundle\Value\EmailData $data);
}
