<?php

namespace Netgen\Bundle\InformationCollectionBundle\Mailer;

interface MailerInterface
{
    /**
     * @return \Swift_Mime_Message
     */
    public function createMessage();

    /**
     * @param \Swift_Mime_Message $message
     *
     * @throws \Netgen\Bundle\InformationCollectionBundle\Exception\EmailNotSentException
     */
    public function sendMessage(\Swift_Mime_Message $message);
}
