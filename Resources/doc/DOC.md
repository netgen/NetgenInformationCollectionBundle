NetgenInformationCollectionBundle
=================================

## Actions

What is action ? Action defines what needs to be done when information collection for is submitted. 
For every content type action list must be defines. When form is submitted, handler travers over list and executes actions. 

### Available actions

This bundle has some already developed actions:

#### Database

Database action stores form data to database, to be specific, to `ezinfocollection` and `ezinfocollection_attribute` tables.

#### Email

Email action sends email with form data to configured email address, that may be set in content type or configuration.
For every content type different Twig template can be applied. Default template for emails must be specified.

Per content type email template configuration example:

```yaml
netgen.default.information_collection.email.ng_feedback_form: 'AcmeBundle:email:ng_feedback_form.html.twig'
netgen.default.information_collection.email.some_other_form: 'AcmeBundle:email:some_other_form.html.twig'
```

Default email template configuration example:

```yaml
netgen.default.information_collection.email.default: 'AcmeBundle:email:default.html.twig'
```

### Creating custom actions

To develop custom action, end developer needs to implement `ActionInterface` 
and define custom action as service tagged with `netgen_information_collection.action` and some custom alias which is used as
action identifier (required for configuration).

Action example:

```php
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
```

And service definition:

```yml
acme_bundle.my_actions.custom:
    class: Acme\Bundle\AcmeBundle\MyCustomActions\CustomAction
    tags:
        - { name: netgen_information_collection.action, alias: custom_action }
```

Configuration:

```yml
netgen_information_collection:
   system:
       default:
           actions_configuration:
               my_content_type:
                   actions:
                       - email
                       - database
                       - custom_action
```

## Field handlers

### Creating custom field handlers