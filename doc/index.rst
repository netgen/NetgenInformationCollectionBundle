.. image:: /_images/ic-logo-color.svg
    :alt: NetgenInformationCollection logo
    :align: center
    :width: 500
    :target: https://github.com/netgen/NetgenInformationCollectionBundle

|

The information collection feature makes it possible to gather user input when a node referencing an information collector object is viewed.
It is typically useful when it comes to the creation of feedback forms, polls, etc.
An object can collect information if at least one of the class attributes is marked as an information collector.

When the object is viewed, each collector attribute will be displayed using the chosen datatype's data collector template.
Instead of just outputting the attributes' contents, the collector templates provide interfaces for data input.
The generated input interface depends on the datatype that represents the attribute.

This bundle re-implements the information collection feature for eZ Platform/Ibexa stack.

.. note::

    This documentation assumes you have a working knowledge of the Symfony
    Framework and eZ Platform DXP. If you're not familiar with Symfony, please start with
    reading the `Quick Tour`_ from the Symfony documentation and for eZ Platform DXP visit the eZ
    Platform `Developer Documentation`_.

.. _`Quick Tour`: https://symfony.com/doc/current/quick_tour
.. _`Developer Documentation`: https://doc.ezplatform.com/


:doc:`Overview </overview/index>`
---------------------------------

.. toctree::
    :hidden:

    overview/index

.. include:: /overview/map.rst.inc


:doc:`Getting started </getting_started/index>`
-----------------------------------------------

.. toctree::
    :hidden:

    getting_started/index

.. include:: /getting_started/map.rst.inc


:doc:`Cookbook </cookbook/index>`
---------------------------------

.. toctree::
    :hidden:

    cookbook/index

.. include:: /cookbook/map.rst.inc


:doc:`Reference </reference/index>`
-----------------------------------

.. toctree::
    :hidden:

    reference/index

.. include:: /reference/map.rst.inc


:doc:`Upgrades </upgrades/index>`
---------------------------------

.. toctree::
    :hidden:

    upgrades/index

.. include:: /upgrades/map.rst.inc
