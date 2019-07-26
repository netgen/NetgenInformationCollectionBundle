<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API;

final class Priority
{
    /**
     * Defines somehow default priority for action.
     */
    public const DEFAULT_PRIORITY = 0;

    /**
     * Defines highest priority for action.
     */
    public const MAX_PRIORITY = 255;

    /**
     * Defines lowest priority for action.
     */
    public const MIN_PRIORITY = -255;
}
