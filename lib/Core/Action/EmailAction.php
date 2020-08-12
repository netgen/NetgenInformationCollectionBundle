<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Action;

use Netgen\InformationCollection\API\Exception\ActionFailedException;
use Netgen\InformationCollection\API\Exception\EmailNotSentException;

final class EmailAction extends BaseEmailAction
{
    public static $defaultName = 'email';

    protected function throwException(EmailNotSentException $exception)
    {
        throw new ActionFailedException(static::$defaultName, $exception->getMessage());
    }
}
