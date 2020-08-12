Builtin info collector actions
==============================

Available actions
-----------------

This bundle has some already developed actions:
* database - stores collected data to database
* email - sends collected data to recipient

Database
~~~~~~~~

Database action stores form data to database, to be specific, to `ezinfocollection` and `ezinfocollection_attribute` tables.

For advanced configuration please refer to [configuration](CONFIGURATION.md) part.

Email
~~~~~

Email action sends email with form data to configured email address, that may be set in content type or configuration.
For every content type different Twig template can be applied. Default template for emails must be specified.

For advanced configuration please refer to [configuration](CONFIGURATION.md) part.

Email template
~~~~~~~~~~~~~~

To define how email would look like you need to create email template. Email body needs to be added inside email block `{% block email %} ... email content ... {% endblock %}`.
Inside template you have exposed:
* `event` - instance of `InformationCollected` class
* `collected_fields` - array of all collected values
* `content` - form content
* `default_variables` - values from configuration like sender, recipient and subject

Some simple email template would look like this:

.. code-block:: twig

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


Sender, recipient and subject
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

By default InformationCollectionBundle will check first if `sender`, `recipient` and `subject` blocks exist inside email template.
If yes it allows you to some custom magic, like this:

.. code-block:: twig

    {% block subject %}
        {# imagine that our subject from configuration equals to 'Information collected' #}
        {# and we want to add value from collected field name #}
        {{ default_variables.subject ~ ' for ' ~ collected_fields.name }}
        {# this will generate 'Information collected for Some name' if Some name was entered on form #}
    {% endblock %}

    {% block email %}

        {# email block #}

    {% endblock %}

In case when none of the blocks exist in template, bundle will use values from content (fields sender, recipient and subject)
and in case when any of those is empty as last fallback values provided in configuration will be used instead.

Crucial actions
~~~~~~~~~~~~~~~

Any custom action can be marked as `Crucial` by implementing `CrucialActionInterface`.
To have crucial action means that if case of failure none of the actions that comes next will be ran.
For example `database` action is marked crucial as there is not point to do any actions if you cannot save data to database.
