<?php

namespace Netgen\Bundle\InformationCollectionBundle\Value;

/**
 * @property string $recipient
 * @property string $subject
 * @property string $sender
 * @property string $body
 */
class EmailData extends ValueObject
{
    /**
     * @var string
     */
    protected $recipient;

    /**
     * @var string
     */
    protected $subject;

    /**
     * @var string
     */
    protected $sender;

    /**
     * @var string
     */
    protected $body;
}
