Actions
=======

### Available actions

This bundle has some already developed actions:

#### Database

Database action stores form data to database, to be specific, to `ezinfocollection` and `ezinfocollection_attribute` tables.

For advanced configuration please refer to [configuration](CONFIGURATION.md) part.

#### Email

Email action sends email with form data to configured email address, that may be set in content type or configuration.
For every content type different Twig template can be applied. Default template for emails must be specified.

For advanced configuration please refer to [configuration](CONFIGURATION.md) part.

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
           actions:
               default:
                   - email
                   - database
               my_content_type:
                   - email
                   - database
                   - custom_action
```
