<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API;

final class Permissions
{
    public const NAME = 'infocollector';
    public const POLICY_READ = 'read';
    public const POLICY_DELETE = 'delete';
    public const POLICY_EXPORT = 'export';
    public const POLICY_ANONYMIZE = 'anonymize';
}
