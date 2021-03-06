<?php

namespace Netgen\InformationCollection\API\Action;

use Netgen\InformationCollection\API\Value\Event\InformationCollected;
use Symfony\Component\Mime\Email;

interface EmailDataProviderInterface
{
    public function provide(InformationCollected $value): Email;
}
