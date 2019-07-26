<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Action;

use Netgen\InformationCollection\API\Action\ActionInterface;
use Netgen\InformationCollection\API\Exception\ActionFailedException;
use Netgen\InformationCollection\API\Exception\EmailNotSentException;
use Netgen\InformationCollection\API\Mailer\MailerInterface;
use Netgen\InformationCollection\API\Value\Event\InformationCollected;
use Netgen\InformationCollection\Core\Factory\AutoResponderDataFactory;

class AutoResponderAction implements ActionInterface
{
    /**
     * @var \Netgen\InformationCollection\API\Mailer\MailerInterface
     */
    protected $mailer;

    /**
     * @var \Netgen\InformationCollection\Core\Factory\AutoResponderDataFactory
     */
    protected $factory;

    /**
     * AutoResponderAction constructor.
     *
     * @param \Netgen\InformationCollection\Core\Factory\AutoResponderDataFactory $factory
     * @param \Netgen\InformationCollection\API\Mailer\MailerInterface $mailer
     */
    public function __construct(AutoResponderDataFactory $factory, MailerInterface $mailer)
    {
        $this->mailer = $mailer;
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function act(InformationCollected $event): void
    {
        $emailData = $this->factory->build($event);

        try {
            $this->mailer->createAndSendMessage($emailData);
        } catch (EmailNotSentException $e) {
            throw new ActionFailedException('auto_responder', $e->getMessage());
        }
    }
}
