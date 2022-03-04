<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Value;

use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo as APIVersionInfo;
use Ibexa\Core\Repository\Values\Content\VersionInfo as CoreVersionInfo;
use Ibexa\Core\Repository\Values\User\User;

class NullUser extends User
{
    /**
     * Returns the VersionInfo for this version.
     */
    public function getVersionInfo(): APIVersionInfo
    {
        return new VersionInfo();
    }
}

class VersionInfo extends CoreVersionInfo
{
    /**
     * {@inheritdoc}
     */
    public function getName($languageCode = null)
    {
        return 'user removed';
    }
}
