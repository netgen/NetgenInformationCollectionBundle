Installation instructions
=========================

Requirements
------------

* PHP 7.4+
* Ibexa Platform 4.0+

Installation steps
------------------

Use Composer
~~~~~~~~~~~~


Run the following from your website root folder to install NetgenInformationCollectionBundle:

.. code-block:: bash

    $ composer require netgen/information-collection-bundle

Include routing configuration
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

In your `config/routes` folder add:

.. code-block:: yaml

    _netgen_information_collection:
        resource: '@NetgenInformationCollectionBundle/Resources/config/routing.yml'


Enable auto_mapping for Doctrine
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Add this to `config.yml`, it will make Doctrine automatically load the mappings from our bundle:

.. code-block:: yaml

    doctrine:
        orm:
            auto_mapping: true
