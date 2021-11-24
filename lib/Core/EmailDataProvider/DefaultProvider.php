<?php

namespace Netgen\InformationCollection\Core\EmailDataProvider;

use EzSystems\EzPlatformContentForms\Data\Content\FieldData;
use Netgen\InformationCollection\API\Action\EmailDataProviderInterface;
use Netgen\InformationCollection\API\Value\Event\InformationCollected;
use Netgen\InformationCollection\Core\Factory\EmailDataFactory;
use Symfony\Component\Mime\Email;

class DefaultProvider implements EmailDataProviderInterface
{
    protected $emailDataFactory;

    public function __construct(
        EmailDataFactory $emailDataFactory
    ) {
        $this->emailDataFactory = $emailDataFactory;
    }

    public function provide(InformationCollected $value): Email
    {
        $emailContent = $this->emailDataFactory->build($value);

        $email = (new Email())
            ->from(...$emailContent->getSender())
            ->to(...$emailContent->getRecipients())
            ->subject($emailContent->getSubject())
            ->html($emailContent->getBody())
            ->text($emailContent->getBody());

        array_map(
            function (FieldData $fieldData) use ($email) {
                $binaryFileValue = $fieldData->value;

                $email->attachFromPath(
                    $binaryFileValue->inputUri,
                    $binaryFileValue->fileName,
                    $binaryFileValue->mimeType
                );
            },
            $emailContent->getAttachments()
        );

        return $email;
    }
}
