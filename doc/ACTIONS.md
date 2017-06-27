Actions
=======

## Available actions

This bundle has some already developed actions:
* database - stores collected data to database
* email - sends collected data to recipient

### Database

Database action stores form data to database, to be specific, to `ezinfocollection` and `ezinfocollection_attribute` tables.

For advanced configuration please refer to [configuration](CONFIGURATION.md) part.

### Email

Email action sends email with form data to configured email address, that may be set in content type or configuration.
For every content type different Twig template can be applied. Default template for emails must be specified.

For advanced configuration please refer to [configuration](CONFIGURATION.md) part.

#### Email template

To define how email would look like you need to create email template. Email body needs to be added inside email block `{% block email %} ... email content ... {% endblock %}`.
Inside template you have exposed:
* `event` - instance of `InformationCollected` class
* `collected_fields` - array of all collected values
* `content` - form content
* `default_variables` - values from configuration like sender, recipient and subject

Some simple email template would look like this:

```twig
{% block email %}

{% for field_name, field_value in collected_fields %}
    {% if content.fields[field_name] is defined and content.fields[field_name] is not empty %}
        <div class="label">
            {{ ez_field_name(content, field_name) }}:
        </div>
        <div class="value">
            {{ field_value }}
        </div>
        <br>
    {% endif %}
{% endfor %}

{% endblock %}

```

#### Sender, recipient and subject

By default InformationCollectionBundle will check first if `sender`, `recipient` and `subject` blocks exist inside email template.
If yes it allows you to some custom magic, like this:

```twig
{% block subject %}
    {# imagine that our subject from configuration equals to 'Information collected' #}
    {# and we want to add value from collected field name #}
    {{ default_variables.subject ~ ' for ' ~ collected_fields.name }}
    {# this will generate 'Information collected for Some name' if Some name was entered on form #}
{% endblock %}

{% block email %}

    {# email block #}

{% endblock %}

```

In case when none of the blocks exist in template, bundle will use values from content (fields sender, recipient and subject) 
and in case when any of those is empty as last fallback values provided in configuration will be used instead.

### Crucial actions

Any custom action can be marked as `Crucial` by implementing `CrucialActionInterface`.
To have crucial action means that if case of failure none of the actions that comes next will be ran.
For example `database` action is marked crucial as there is not point to do any actions if you cannot save data to database.

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
               content_types:
                   my_content_type:
                       - email
                       - database
                       - custom_action
```
