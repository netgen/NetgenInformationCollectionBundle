Configuration
=============

Set siteaccess aware configuration
----------------------------------

Here is sample configuration for actions, the developer will need to define a list of actions to be run depending on the content type.
Configuration needs to be added in `app/config/config.yml` or `app/config/ezplatform.yml`:


.. code-block:: yaml

    netgen_information_collection:
        system:
            default:
                action_config:
                    email:
                        templates:
                            default: '@Acme/email/default.html.twig'
                        default_variables:
                            sender: 'sender@example.com'
                            recipient: 'recipient@example.com'
                            subject: 'Subject'
                actions:
                    default:
                        - email
                        - database

Don't forget to create default email template.

Clear the caches
----------------

Clear the eZ Publish caches with the following command:

.. code-block:: bash

    $ php app/console cache:clear

For more detailed configuration, please check documentation reference.
