<?php

namespace Netgen\InformationCollection\API\Value;

use eZ\Publish\Core\Repository\Values\Content\VersionInfo as CoreVersionInfo;
use eZ\Publish\API\Repository\Values\Content\VersionInfo as APIVersionInfo;
use eZ\Publish\Core\Repository\Values\User\User;

class NullUser extends User
{
    /**
     * Returns the VersionInfo for this version.
     *
     * @return \eZ\Publish\API\Repository\Values\Content\VersionInfo
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
