<?php

namespace Netgen\Bundle\InformationCollectionBundle\Action;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;
use Netgen\Bundle\InformationCollectionBundle\Factory\EmailDataFactory;
use Netgen\Bundle\InformationCollectionBundle\FieldHandler\Legacy\Registry\FieldHandlerRegistry;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Templating\DelegatingEngine;

class EmailAction implements ActionInterface
{
    /**
     * @var FieldHandlerRegistry
     */
    protected $fieldHandlerRegistry;

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
    protected $emailDataFactory;

    /**
     * SendEmailAction constructor.
     *
     * @param FieldHandlerRegistry $fieldHandlerRegistry
     * @param EmailDataFactory $emailDataFactory
     * @param Swift_Mailer $mailer
     * @param DelegatingEngine $template
     * @param ContentService $contentService
     */
    public function __construct(
        FieldHandlerRegistry $fieldHandlerRegistry,
        EmailDataFactory $emailDataFactory,
        Swift_Mailer $mailer,
        DelegatingEngine $template,
        ContentService $contentService
    )
    {
        $this->fieldHandlerRegistry = $fieldHandlerRegistry;
        $this->mailer = $mailer;
        $this->template = $template;
        $this->contentService = $contentService;
        $this->emailDataFactory = $emailDataFactory;
    }

    /**
     * @inheritDoc
     */
    public function act(InformationCollected $event)
    {
        $location = $event->getLocation();
        $contentType = $event->getContentType();
        $content = $this->contentService->loadContent($location->contentId);

        $emailData = $this->emailDataFactory->build($content);

        $message = Swift_Message::newInstance();
        $message->setSubject($emailData->getSubject());
        $message->setTo($emailData->getRecipient());
        $message->setFrom($emailData->getSender());

        $message->setBody(
            $this->template->render( $emailData->getTemplate(),
                [
                    'content' => $content,
                    'content_type' => $contentType,
                    'params' => $event->getInformationCollectionStruct()->getCollectedFields(),
                    'additional_content' => $event->getAdditionalContent(),
                ]
            ),
            'text/html'
        );

        return $this->mailer->send( $message );
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'email';
    }
}