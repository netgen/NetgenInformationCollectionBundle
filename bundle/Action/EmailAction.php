<?php

namespace Netgen\Bundle\InformationCollectionBundle\Action;

use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;
use Netgen\Bundle\InformationCollectionBundle\Exception\ActionFailedException;
use Netgen\Bundle\InformationCollectionBundle\Exception\EmailNotSentException;
use Netgen\Bundle\InformationCollectionBundle\Factory\EmailDataFactory;
use Netgen\Bundle\InformationCollectionBundle\Mailer\MailerInterface;

class EmailAction implements ActionInterface
{
    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\Mailer\MailerInterface
     */
    protected $mailer;

    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\Factory\EmailDataFactory
     */
    protected $factory;

    /**
     * SendEmailAction constructor.
     *
     * @param \Netgen\Bundle\InformationCollectionBundle\Factory\EmailDataFactory $factory
     * @param \Netgen\Bundle\InformationCollectionBundle\Mailer\MailerInterface $mailer
     */
    public function __construct(EmailDataFactory $factory, MailerInterface $mailer)
    {
        $this->mailer = $mailer;
        $this->factory = $factory;
    }

    /**
     * @inheritDoc
     */
    public function act(InformationCollected $event)
    {
        $emailData = $this->factory->build($event);

        $message = $this->mailer->createMessage();
        $message->setSubject($emailData->getSubject());
        $message->setTo($emailData->getRecipient());
        $message->setFrom($emailData->getSender());
        $message->setBody($emailData->getBody(), 'text/html');

        try {

            $this->mailer->sendMessage($message);

        } catch (EmailNotSentException $e) {

            throw new ActionFailedException();

        }
    }
}
