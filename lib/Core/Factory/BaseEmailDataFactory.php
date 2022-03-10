<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Factory;

use Netgen\InformationCollection\API\Factory\EmailContentFactoryInterface;
use Netgen\InformationCollection\API\Value\DataTransfer\EmailContent;
use Netgen\InformationCollection\API\Value\Event\InformationCollected;

abstract class BaseEmailDataFactory implements EmailContentFactoryInterface
{
    abstract public function build(InformationCollected $value): EmailContent;
}
