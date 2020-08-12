Creating a custom action
========================

To develop custom action, end developer needs to implement `ActionInterface`
and define custom action as service tagged with `netgen_information_collection.action` and some custom alias which is used as
action identifier (required for configuration).

Action example:

.. code-block:: php

    <?php

    namespace Acme\Bundle\AcmeBundle\MyCustomActions;

    use Netgen\Bundle\InformationCollectionBundle\Action\ActionInterface;
    use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;

    class CustomAction implements ActionInterface
    {

        /**
         * @inheritDoc
         */
        public function act(InformationCollected $event)
        {
            // do some magic ...
        }
    }

And service definition:

.. code-block:: yaml

    acme_bundle.my_actions.custom:
        class: Acme\Bundle\AcmeBundle\MyCustomActions\CustomAction
        tags:
            - { name: netgen_information_collection.action, alias: custom_action }


Configuration:

.. code-block:: yaml

    netgen_information_collection:
       system:
           default:
               actions:
                   default:
                       - email
                       - database
                   content_types:
                       my_content_type:
                           - email
                           - database
                           - custom_action

