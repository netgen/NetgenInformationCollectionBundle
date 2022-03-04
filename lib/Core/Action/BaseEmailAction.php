<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Action;

use Netgen\InformationCollection\API\Action\ActionInterface;
use Netgen\InformationCollection\API\Exception\EmailNotSentException;
use Netgen\InformationCollection\API\MailerInterface;
use Netgen\InformationCollection\API\Value\Event\InformationCollected;
use Netgen\InformationCollection\Core\Factory\BaseEmailDataFactory;

abstract class BaseEmailAction implements ActionInterface
{
    /**
     * @var \Netgen\InformationCollection\API\MailerInterface
     */
    protected $mailer;

    /**
     * @var \Netgen\InformationCollection\Core\Factory\BaseEmailDataFactory
     */
    protected $factory;

    public function __construct(MailerInterface $mailer, BaseEmailDataFactory $factory)
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
            $this->throwException($e);
        }
    }

    abstract protected function throwException(EmailNotSentException $exception);
}
