Configuration reference
=======================

Layout type configuration
-------------------------

The following lists all available layout type configuration options:

.. code-block:: yaml

    netgen_layouts:
        layout_types:
            # Layout type identifier
            my_layout:
                # The switch to enable or disable showing of the layout type in the interface
                enabled: true

                # Layout type name, required
                name: 'My layout'

                # The full path to layout icon
                icon: '/path/to/icon/my_layout.svg'

                # A collection of zones available in the layout type, required
                zones:
                    # Zone identifier
                    left:
                        # Zone name, required
                        name: 'Left'

                        # List of block definitions which are allowed in the zone
                        allowed_block_definitions: []
                    right:
                        name: 'Right'
                        allowed_block_definitions: [title]

Block definition configuration
------------------------------

The following lists all available block definition configuration options:

.. code-block:: yaml

    netgen_layouts:
        block_definitions:
            # Block definition identifier
            my_block:
                # The switch to enable or disable showing of all block types
                # related to block definition in the interface
                enabled: true

                # Identifier of a handler which the block definition will use.
                # The value used here needs to be the same as the identifier
                # specified in handler tag in Symfony DIC.
                # If undefined, the handler with the same identifier as the
                # block definition itself will be used.
                handler: ~

                # Block definition name, required
                name: 'My block'

                # The full path to block definition icon
                icon: '/path/to/icon/my_block.svg'

                # Specifies if the block will be translatable once created
                translatable: false

                # The list of collections the block has. Only one collection named
                # "default" is supported for now. Omit the config to disable the collection.

                collections:
                    default:
                        # The list of valid items for the collection. Use null to
                        # allow all items, an empty array to disable adding manual
                        # items, and a list of items to limit the collection to
                        # to only those items.
                        valid_item_types: null

                        # The list of valid query types for the collection. Use null to
                        # allow all query types, an empty array to disable dynamic collections,
                        # and a list of query types to limit the collection to
                        # only those query types.
                        valid_query_types: null

                # This controls which forms will be available to the block.
                # You can either enable the full form, or content and design forms.
                # If full form is enabled, all block options in the right sidebar
                # in the layout editing app will be shown at once, otherwise,
                # Content and Design tabs will be created in the right sidebar
                forms:
                    full:
                        enabled: true
                        type: Netgen\Layouts\Block\Form\FullEditType
                    design:
                        enabled: false
                        type: Netgen\Layouts\Block\Form\DesignEditType
                    content:
                        enabled: false
                        type: Netgen\Layouts\Block\Form\ContentEditType

                # The list of all view types in a block definition, required
                view_types:

                    # View type identifier
                    my_view_type:
                        # Switch to control if the view type is shown in the interface or not
                        enabled: true

                        # View type name, required
                        name: 'My view type'

                        # The list of allowed item view types for this block view type
                        item_view_types:

                            # Item view type identifier
                            my_item_view_type:
                                # Switch to control if the item view type is shown in the interface or not
                                enabled: true

                                # Item view type name, required
                                name: 'My item view type'

                        # Use this configuration to control which block parameters will be displayed
                        # when editing a block in specified view type. Use null to display all
                        # parameters, an empty array to hide all parameters and a list of parameter
                        # names to list specific parameters to show. You can also prefix the parameter
                        # with exclamation mark to exclude it.
                        valid_parameters: null

Block type and block type group configuration
---------------------------------------------

The following lists all available block type and block type group configuration options:

.. code-block:: yaml

    netgen_layouts:
        block_types:
            # Block type identifier
            my_block_type:
                # The switch to enable or disable showing the block type in the interface
                enabled: true

                # Block type name, if undefined, will use the name of a block definition
                # with the same identifier as the block type itself.
                name: ~

                # The full path to block type icon
                icon: '/path/to/icon/my_block_type.svg'

                # Block definition identifier of the block type, if undefined, will use the
                # block definition with the same identifier as the block type itself.
                definition_identifier: ~

                # Default values for the block
                defaults:

                    # Default name (label) of the block
                    name: ''

                    # Default view type of the block. If empty, will use the first available view type.
                    view_type: ''

                    # Default item view type of items inside the block. If empty, will use the first
                    # available item view type in regards to chosen block view type.
                    item_view_type: ''

                    # Default values for block parameters
                    parameters:
                        param1: value1
                        param2: value2

        block_type_groups:

            # Block type group identifier
            my_group:

                # The switch to enable or disable showing the block type group in the interface
                enabled: true

                # Block type group name, required
                name: 'My group'

                # List of block types to show inside the group
                block_types: [my_type_1, my_type_2]

Query type configuration
------------------------

The following lists all available query type configuration options:

.. code-block:: yaml

    netgen_layouts:
        query_types:
            # Query type identifier
            my_query_type:
                # The switch to enable or disable showing of the query type in the interface
                enabled: true

                # Identifier of a handler which the query type will use.
                # The value used here needs to be the same as the identifier
                # specified in handler tag in Symfony DIC.
                # If undefined, the handler with the same identifier as the
                # query type itself will be used.
                handler: ~

                # Query type name, required
                name: 'My query type'

Value type configuration
------------------------

The following lists all available value type configuration options:

.. code-block:: yaml

    netgen_layouts:
        # The list of value types available to build items from
        value_types:
            # Value type identifier
            my_value_type:
                # Value type name, required
                name: 'My value type'

                # The switch to enable or disable showing the value type in the interface
                enabled: true

                # The switch to enable or disable support for manual items. If disabled,
                # the system will not require for you to implement Content Browser support
                # for manually selecting items
                manual_items: true

Design configuration
--------------------

The following lists all available design configuration options:

.. code-block:: yaml

    netgen_layouts:
        # The list of all designs available in the system
        design_list:
            # Key is the design identifier, while value is the list of all
            # themes available for the design. Note that ``standard`` theme
            # is automatically included as a fallback and there's no need to
            # specify it
            my_design: [theme1, theme2]

        # Specifies which design, from the list of configured designs, is currently active
        design: my_design

.. tip::

    In eZ Platform integration, currently active design is siteaccess aware,
    meaning, you can use configuration similar to this:

    .. code-block:: yaml

        netgen_layouts:
            system:
                cro:
                    design: my_design
                eng:
                    design: my_other_design

Administration interface configuration
--------------------------------------

The following lists all available configuration options for Netgen Layouts
admin interface:

.. code-block:: yaml

    netgen_layouts:
        admin:
            # The list of JavaScript files which will be injected into admin interface
            javascripts:
                - /path/to/script1.js
                - /path/to/script2.js

            # The list of stylesheets which will be injected into admin interface
            stylesheets:
                - /path/to/style1.css
                - /path/to/style2.css

Layout editing app interface configuration
------------------------------------------

The following lists all available configuration options for Netgen Layouts
layout editing interface:

.. code-block:: yaml

    netgen_layouts:
        app:
            # The list of JavaScript files which will be injected into layout editing interface
            javascripts:
                - /path/to/script1.js
                - /path/to/script2.js

            # The list of stylesheets which will be injected into layout editing interface
            stylesheets:
                - /path/to/style1.css
                - /path/to/style2.css

Other configuration
-------------------

The following lists assorted configuration options that do not fit in other categories:

.. code-block:: yaml

    netgen_layouts:
        # This flag activates debug mode in Netgen Layouts. This flag is primarily used
        # for development of Netgen Layouts themselves and is not useful in project context
        # and should be kept disabled
        debug: false

        # This configures the main pagelayout of your app which resolved layout templates
        # will extend and which will be used as a fallback if no layout is resolved
        pagelayout: '@App/my_pagelayout.html.twig'

        # Generic configuration used for specifying various API keys for 3rd party services.
        # Currently used only internally, and cannot be extended.
        api_keys:
            # API key used for displaying a Google Maps map inside the Maps block
            google_maps: 'foo'


Configuration
-------------

Some advanced use cases.

Specifiying which action to run per content type

By default all actions set by *default* under *actions* tree will be executed for all content types.

.. code-block:: yaml

    actions:
        default:
            - email
            - database

To run only specific action for given content type, for example *my_content_type* and *email* add this to configuration:

.. code-block:: yaml

    actions:
        default:
            - email
            - database
        content_types:
            my_content_type:
                - email

Or to execute only *database* action for *my_content_type_2*:

.. code-block:: yaml

    actions:
        default:
            - email
            - database
        content_types:
            my_content_type:
                - email
            my_content_type_2:
                - database

## Specifiying which email templates to use per content type

In case when want to split email templates per content type, rather than using default one all, add this to configuration:

.. code-block:: yaml

    action_config:
        email:
            templates:
                default: '@Acme/email/default.html.twig'
                content_types:
                    my_content_type: '@Acme/email/my_content_type.html.twig'
                    my_content_type2: '@Acme/email/my_content_type2.html.twig'
