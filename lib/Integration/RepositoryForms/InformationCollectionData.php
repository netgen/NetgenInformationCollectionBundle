<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Integration\RepositoryForms;

use eZ\Publish\Core\Repository\Values\Content\ContentUpdateStruct;
use EzSystems\RepositoryForms\Data\Content\ContentData;

/**
 * Class InformationCollectionData
 *
 * @property-read \eZ\Publish\Core\Repository\Values\Content\ContentProxy $contentDraft
 */
class InformationCollectionData extends ContentUpdateStruct
{
    use ContentData;

    /**
     * @var \eZ\Publish\Core\Repository\Values\Content\ContentProxy
     */
    protected $contentDraft;
}
