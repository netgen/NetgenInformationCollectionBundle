Symfony dependency injection tags
=================================

The following lists all dependency injection tags and their usage available
in Netgen Information Collection:

.. note::

    All available Symfony dependency injection tags defined by Netgen Information Collection Bundle
    are registered for autoconfiguration. So if, within you service definition file have autoconfiguration
    enabled, your services are going to be tagged automatically. For more information about autoconfiguration
    please refer to `Symfony documentation`_.

.. _`Symfony documentation`: https://symfony.com/doc/current/service_container.html#the-autoconfigure-option


``netgen_information_collection.action``
----------------------------------------

**Purpose**: Adds a new block definition handler

When registering a new block definition handler, you need to use the
``identifier`` attribute in the tag to specify the unique identifier of the
block definition:

.. code-block:: yaml

    app.block.block_definition.handler.my_block:
        class: AppBundle\Block\BlockDefinition\Handler\MyBlockHandler
        tags:
            - { name: netgen_layouts.block_definition_handler, identifier: my_block }

``netgen_information_collection.anonymizer.visitor.field``
----------------------------------------------------------

**Purpose**: Adds a new block handler plugin

When registering a new block definition handler plugin, you can use the
``priority`` attribute in the tag to specify the order in which your handler
plugin is executed in regard to other existing plugins:

.. code-block:: yaml

    app.block.block_definition.handler.plugin.my_plugin:
        class: AppBundle\Block\BlockDefinition\Handler\MyPlugin
        tags:
            - { name: netgen_layouts.block_definition_handler.plugin, priority: 10 }

``netgen_information_collection.field_handler.custom``
------------------------------------------------------

**Purpose**: Adds a new query type handler

When registering a new query type handler, you need to use the ``type``
attribute in the tag to specify the unique identifier of the query type:

.. code-block:: yaml

    app.collection.query_type.handler.my_handler:
        class: AppBundle\Collection\QueryType\Handler\MyHandler
        tags:
            - { name: netgen_layouts.query_type_handler, type: my_handler }

``netgen_information_collection.export.formatter``
--------------------------------------------------

**Purpose**: Adds a new parameter type

.. code-block:: yaml

    app.parameters.parameter_type.my_type:
        class: AppBundle\Parameters\ParameterType\MyType
        tags:
            - { name: netgen_layouts.parameter_type }
