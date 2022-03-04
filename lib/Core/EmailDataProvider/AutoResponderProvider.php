<?php

declare(strict_types=1);

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
        $headers->add('Content-Type', '');

        return $email;
    }
}
