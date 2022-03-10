<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Action;

use Netgen\InformationCollection\API\Action\ActionInterface;
use Netgen\InformationCollection\API\Exception\EmailNotSentException;
use Netgen\InformationCollection\API\Factory\EmailContentFactoryInterface;
use Netgen\InformationCollection\API\MailerInterface;
use Netgen\InformationCollection\API\Value\Event\InformationCollected;

abstract class BaseEmailAction implements ActionInterface
{
    protected MailerInterface $mailer;

    protected EmailContentFactoryInterface $factory;

    public function __construct(MailerInterface $mailer, EmailContentFactoryInterface $factory)
    {
        $this->mailer = $mailer;
        $this->factory = $factory;
    }

    public function act(InformationCollected $event): void
    {
        $emailData = $this->factory->build($event);

        try {
            $this->mailer->createAndSendMessage($emailData);
        } catch (EmailNotSentException $e) {
            $this->throwException($e);
        }
    }

    abstract protected function throwException(EmailNotSentException $exception);
}
