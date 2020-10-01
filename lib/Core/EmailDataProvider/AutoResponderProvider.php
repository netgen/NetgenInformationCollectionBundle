<?php


namespace Netgen\InformationCollection\Core\EmailDataProvider;

use Netgen\InformationCollection\API\Action\EmailDataProviderInterface;
use Netgen\InformationCollection\API\Value\Event\InformationCollected;
use Symfony\Component\Mime\Email;

class AutoResponderProvider implements EmailDataProviderInterface
{
    public function provide(InformationCollected $value): Email
    {
        $email = new Email();

        $headers = $email->getHeaders();

        $header = new Header
        $headers->add('Content-Type', '');

        return $email;
    }
}
