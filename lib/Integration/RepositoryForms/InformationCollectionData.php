<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Integration\RepositoryForms;

use eZ\Publish\Core\Repository\Values\Content\ContentUpdateStruct;
use EzSystems\RepositoryForms\Data\Content\ContentData;

class InformationCollectionData extends ContentUpdateStruct
{
    use ContentData;

    protected $contentDraft;
}
