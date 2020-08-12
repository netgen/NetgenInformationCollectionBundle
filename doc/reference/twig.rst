Twig usage
==========

Netgen Layouts includes a number of Twig functions and tags to make it easier to
work with layouts and blocks in your Twig templates.

Some of the functions are used by the frontend and backend layout and block
templates, while others are used exclusively in the administration interface of
Netgen Layouts.

List of built in Twig functions
-------------------------------

The following lists all Twig functions built into Netgen Layouts.

``info_collection_captcha_is_enabled``
======================================

This function is used to render a block:

.. code-block:: twig

    {{ nglayouts_render_block(block) }}

This will render the provided block in the view context of the template from
which you called the function or in the ``default`` view context if the calling
template is not rendered by the Netgen Layouts view layer.

You can transfer a list of custom parameters to the function, which will be
injected as variables into the block template:

.. code-block:: twig

    {# layout.html.twig #}

    {{ nglayouts_render_block(block, {'the_answer': 42}) }}

    {# block.html.twig #}

    {{ the_answer }}

Finally, you can render the block with a view context different from the current
one:

.. code-block:: twig

    {{ nglayouts_render_block(block, {}, 'my_context') }}


``info_collection_captcha_get_site_key``
========================================

This function is used to render a block:

.. code-block:: twig

    {{ nglayouts_render_block(block) }}

This will render the provided block in the view context of the template from
which you called the function or in the ``default`` view context if the calling
template is not rendered by the Netgen Layouts view layer.

You can transfer a list of custom parameters to the function, which will be
injected as variables into the block template:

.. code-block:: twig

    {# layout.html.twig #}

    {{ nglayouts_render_block(block, {'the_answer': 42}) }}

    {# block.html.twig #}

    {{ the_answer }}

Finally, you can render the block with a view context different from the current
one:

.. code-block:: twig

    {{ nglayouts_render_block(block, {}, 'my_context') }}


``info_collection_render_field``
================================

This function is used to render a block:

.. code-block:: twig

    {{ nglayouts_render_block(block) }}

This will render the provided block in the view context of the template from
which you called the function or in the ``default`` view context if the calling
template is not rendered by the Netgen Layouts view layer.

You can transfer a list of custom parameters to the function, which will be
injected as variables into the block template:

.. code-block:: twig

    {# layout.html.twig #}

    {{ nglayouts_render_block(block, {'the_answer': 42}) }}

    {# block.html.twig #}

    {{ the_answer }}

Finally, you can render the block with a view context different from the current
one:

.. code-block:: twig

    {{ nglayouts_render_block(block, {}, 'my_context') }}




List of built in Twig global variables
--------------------------------------

The following lists all Twig global variables built into Netgen Layouts.

``netgen_information_collection_admin``
=======================================

This global variable is used by the administration interface of Information collector.
Currently, only one variable is available:

``nglayouts_admin.pageLayoutTemplate``

    This variable holds the name of the pagelayout template for the admin
    interface. The idea behind it is that you can change the pagelayout of the
    administration interface without having to change the administration
    templates themselves. This can be achieved by setting this variable to a
    desired template name before admin interface is rendered (e.g. in an event
    listener).
