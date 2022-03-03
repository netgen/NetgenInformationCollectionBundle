<?php

namespace Netgen\Bundle\InformationCollectionBundle\Action;

use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;
use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollectedInterface;
use Netgen\Bundle\InformationCollectionBundle\Exception\ActionFailedException;
use Netgen\Bundle\InformationCollectionBundle\Factory\AutoResponderDataFactory;
use Netgen\Bundle\InformationCollectionBundle\Factory\FactoryInterface;
use Netgen\Bundle\InformationCollectionBundle\Mailer\MailerInterface;
use Netgen\Bundle\InformationCollectionBundle\Exception\EmailNotSentException;

class AutoResponderAction implements ActionInterface
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
     * AutoResponderAction constructor.
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
            throw new ActionFailedException('auto_responder', $e->getMessage());
        }
    }
}
