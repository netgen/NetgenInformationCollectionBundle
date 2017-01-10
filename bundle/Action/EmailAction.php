<?php

namespace Netgen\Bundle\InformationCollectionBundle\Action;

use eZ\Publish\API\Repository\ContentService;
use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;
use Netgen\Bundle\InformationCollectionBundle\Factory\EmailDataFactory;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Templating\DelegatingEngine;

class EmailAction implements ActionInterface
{
    /**
     * @var Swift_Mailer
     */
    protected $mailer;

    /**
     * @var DelegatingEngine
     */
    protected $template;

    /**
     * @var ContentService
     */
    protected $contentService;

    /**
     * @var EmailDataFactory
     */
    protected $factory;

    /**
     * SendEmailAction constructor.
     *
     * @param EmailDataFactory $factory
     * @param Swift_Mailer $mailer
     * @param DelegatingEngine $template
     * @param ContentService $contentService
     */
    public function __construct(
        EmailDataFactory $factory,
        Swift_Mailer $mailer,
        DelegatingEngine $template,
        ContentService $contentService
    ) {
    
        $this->mailer = $mailer;
        $this->template = $template;
        $this->contentService = $contentService;
        $this->factory = $factory;
    }

    /**
     * @inheritDoc
     */
    public function act(InformationCollected $event)
    {
        $location = $event->getLocation();
        $contentType = $event->getContentType();
        $content = $this->contentService->loadContent($location->contentId);

        $emailData = $this->factory->build($content);

        $message = Swift_Message::newInstance();
        $message->setSubject($emailData->getSubject());
        $message->setTo($emailData->getRecipient());
        $message->setFrom($emailData->getSender());

        $message->setBody(
            $this->template->render(
                $emailData->getTemplate(),
                [
                    'content' => $content,
                    'content_type' => $contentType,
                    'params' => $event->getInformationCollectionStruct()->getCollectedFields(),
                    'additional_content' => $event->getAdditionalContent(),
                ]
            ),
            'text/html'
        );

        return $this->mailer->send($message);
    }
}
