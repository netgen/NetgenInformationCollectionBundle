<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Action;

use Netgen\InformationCollection\API\Action\ActionInterface;
use Netgen\InformationCollection\API\Exception\EmailNotSentException;
use Netgen\InformationCollection\API\MailerInterface;
use Netgen\InformationCollection\API\Value\Event\InformationCollected;
use Netgen\InformationCollection\Core\EmailDataProvider\DefaultProvider;
use Netgen\InformationCollection\Core\Factory\BaseEmailDataFactory;

abstract class BaseEmailAction implements ActionInterface
{
    /**
     * @var \Netgen\InformationCollection\API\MailerInterface
     */
    protected $mailer;

    /**
     * @var DefaultProvider
     */
    protected $emailDataProvider;


    public function __construct(MailerInterface $mailer, DefaultProvider $emailDataProvider)
    {
        $this->mailer = $mailer;
        $this->emailDataProvider = $emailDataProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function act(InformationCollected $event): void
    {
        $emailData = $this->emailDataProvider->provide($event);

        try {
            $this->mailer->sendEmail($emailData);
        } catch (EmailNotSentException $e) {
            $this->throwException($e);
        }
    }

    abstract protected function throwException(EmailNotSentException $exception);
}
