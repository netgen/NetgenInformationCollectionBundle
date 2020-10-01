<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API;

final class Events
{
    /**
     * The INFORMATION_COLLECTED event occurs just after the information collection has been submitted.
     *
     * The event listener method receives a \Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected
     */
    public const INFORMATION_COLLECTED = 'netgen_information_collection.events.information_collected';

    /**
     * The BEFORE_ACTION_EXECUTION event occurs before the action execution.
     */
    public const BEFORE_ACTION_EXECUTION = 'netgen_information_collection.events.before_action_execution';

    /**
     * The ACTION_EXECUTION event occurs after the action execution.
     */
    public const AFTER_ACTION_EXECUTION = 'netgen_information_collection.events.action_execution';

    /**
     * The ACTION_EXECUTION_FAIL event occurs when the action fails.
     */
    public const ACTION_EXECUTION_FAIL = 'netgen_information_collection.events.action_execution_fail';

    /**
     * The CRUCIAL_ACTION_EXECUTION_FAIL event occurs when the action marked as crucial fails.
     */
    public const CRUCIAL_ACTION_EXECUTION_FAIL = 'netgen_information_collection.events.crucial_action_execution_fail';

    /**
     * The BEFORE_EMAIL_ACTION event occurs when the email action has started executing, Email data was prepared but email itself is still not sent.
     */
    public const BEFORE_EMAIL_ACTION = 'netgen_information_collection.events.before_email_action';

    /**
     * The BEFORE_AUTO_RESPONDER_ACTION event occurs when the auto responder action has started executing, Email data was prepared but email itself is still not sent.
     */
    public const BEFORE_AUTO_RESPONDER_ACTION = 'netgen_information_collection.events.before_auto_responder_action';

}

