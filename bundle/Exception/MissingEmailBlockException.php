<?php

namespace Netgen\Bundle\InformationCollectionBundle\Exception;

use RuntimeException;

class MissingEmailBlockException extends RuntimeException
{
    /**
     * MissingEmailBlockException constructor.
     *
     * @param string $template
     * @param array $blocks
     */
    public function __construct($template, array $blocks)
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
