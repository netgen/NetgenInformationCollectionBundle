<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Action;

use Netgen\InformationCollection\API\Value\Event\InformationCollected;
use Netgen\InformationCollection\API\Exception\ActionFailedException;
use Netgen\InformationCollection\API\Action\ActionInterface;
use Netgen\InformationCollection\API\Exception\EmailNotSentException;
use Netgen\InformationCollection\Core\Factory\EmailDataFactory;
use Netgen\InformationCollection\API\Mailer\MailerInterface;

class EmailAction implements ActionInterface
{
    /**
     * @var \Netgen\InformationCollection\API\Mailer\MailerInterface
     */
    protected $mailer;

    /**
     * @var \Netgen\InformationCollection\Core\Factory\EmailDataFactory
     */
    protected $factory;

    /**
     * EmailAction constructor.
     *
     * @param \Netgen\InformationCollection\Core\Factory\EmailDataFactory $factory
     * @param \Netgen\InformationCollection\API\Mailer\MailerInterface $mailer
     */
    public function __construct(EmailDataFactory $factory, MailerInterface $mailer)
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
            throw new ActionFailedException('email', $e->getMessage());
        }
    }
}
