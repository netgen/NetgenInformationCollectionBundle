<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API;

final class Constants
{
    /**
     * Recipient field identifier.
     */
    public const FIELD_RECIPIENT = 'recipient';

    /**
     * Sender field identifier.
     */
    public const FIELD_SENDER = 'sender';

    /**
     * Subject field identifier.
     */
    public const FIELD_SUBJECT = 'subject';

    /**
     * Auto responder subject field identifier.
     */
    public const FIELD_AUTO_RESPONDER_SUBJECT = 'auto_responder_subject';

    /**
     * Email field type.
     */
    public const FIELD_TYPE_EMAIL = 'email';

    /**
     * Text field type.
     */
    public const FIELD_TYPE_TEXT = 'text';

    /**
     * Block email.
     */
    public const BLOCK_EMAIL = 'email';

    /**
     * Block recipient.
     */
    public const BLOCK_RECIPIENT = 'recipient';

    /**
     * Block sender.
     */
    public const BLOCK_SENDER = 'sender';

    /**
     * Block subject.
     */
    public const BLOCK_SUBJECT = 'subject';
}
