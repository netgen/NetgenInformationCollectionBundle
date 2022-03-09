<?php

namespace Netgen\Bundle\InformationCollectionBundle\Action;

use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;
use Netgen\Bundle\InformationCollectionBundle\Exception\ActionFailedException;
use Netgen\Bundle\InformationCollectionBundle\Exception\EmailNotSentException;
use Netgen\Bundle\InformationCollectionBundle\Factory\EmailDataFactoryInterface;
use Netgen\Bundle\InformationCollectionBundle\Mailer\MailerInterface;

class EmailAction implements ActionInterface
{
    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\Mailer\MailerInterface
     */
    protected $mailer;

    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\Factory\EmailDataFactoryInterface
     */
    protected $factory;

    /**
     * EmailAction constructor.
     *
     * @param \Netgen\Bundle\InformationCollectionBundle\Factory\EmailDataFactoryInterface $factory
     * @param \Netgen\Bundle\InformationCollectionBundle\Mailer\MailerInterface $mailer
     */
    public function __construct(EmailDataFactoryInterface $factory, MailerInterface $mailer)
    {
        $this->mailer = $mailer;
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function act(InformationCollected $event)
    {
        $emailData = $this->factory->build($event);

        try {
            $this->mailer->createAndSendMessage($emailData);
        } catch (EmailNotSentException $e) {
            throw new ActionFailedException('email', $e->getMessage());
        }
    }
}
