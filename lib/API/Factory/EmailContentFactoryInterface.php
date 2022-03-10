<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Factory;

use Netgen\InformationCollection\API\Value\DataTransfer\EmailContent;
use Netgen\InformationCollection\API\Value\Event\InformationCollected;

interface EmailContentFactoryInterface
{
    /**
     * Factory method.
     *
     * @param InformationCollected $value
     *
     * @return EmailContent
     */
    public function build(InformationCollected $value): EmailContent;
}
