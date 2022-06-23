<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Exception;

use RuntimeException;
use function count;
use function implode;

class MissingEmailBlockException extends RuntimeException
{
    public function __construct(string $template, array $blocks)
    {
        $blocksCount = count($blocks);
        $verb = 'is';
        $blocks = implode(', ', $blocks);

        if ($blocksCount > 1) {
            $verb = 'are';
        } elseif ($blocksCount < 1) {
            $blocks = 'none';
        }

        $message = "Missing email block in {$template} template, currently there {$verb} {$blocks} available.";
        parent::__construct($message);
    }
}
