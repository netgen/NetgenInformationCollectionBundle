<?php

namespace Netgen\Bundle\InformationCollectionBundle\Action;

use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;
use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollectedInterface;
use Netgen\Bundle\InformationCollectionBundle\Exception\ActionFailedException;
use Netgen\Bundle\InformationCollectionBundle\Exception\EmailNotSentException;
use Netgen\Bundle\InformationCollectionBundle\Factory\EmailDataFactory;
use Netgen\Bundle\InformationCollectionBundle\Factory\FactoryInterface;
use Netgen\Bundle\InformationCollectionBundle\Mailer\MailerInterface;

class EmailAction implements ActionInterface
{
    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\Mailer\MailerInterface
     */
    protected $mailer;

    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\Factory\FactoryInterface
     */
    protected $factory;

    /**
     * EmailAction constructor.
     *
     * @param \Netgen\Bundle\InformationCollectionBundle\Factory\FactoryInterface $factory
     * @param \Netgen\Bundle\InformationCollectionBundle\Mailer\MailerInterface $mailer
     */
    public function __construct(FactoryInterface $factory, MailerInterface $mailer)
    {
        $this->mailer = $mailer;
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function act(InformationCollectedInterface $event)
    {
        $emailData = $this->factory->build($event);

        try {
            $this->mailer->createAndSendMessage($emailData);
        } catch (EmailNotSentException $e) {
            throw new ActionFailedException('email', $e->getMessage());
        }
    }
}
