Installation instructions
=========================

Requirements
------------

* PHP 7.3+
* eZ Platform 2.0+

Installation steps
------------------

Use Composer
~~~~~~~~~~~~


Run the following from your website root folder to install NetgenInformationCollectionBundle:

.. code-block:: bash

    $ composer require netgen/information-collection-bundle


Activate the bundle
~~~~~~~~~~~~~~~~~~~

Activate required bundles in `app/AppKernel.php` file by adding them to the `$bundles` array in `registerBundles` method:

.. code-block:: php

    public function registerBundles()
    {
        ...
        $bundles[] = new Netgen\Bundle\EzFormsBundle\NetgenEzFormsBundle();
        $bundles[] = new Netgen\Bundle\InformationCollectionBundle\NetgenInformationCollectionBundle();

        return $bundles;
    }


Include routing configuration
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~


In your main routing configuration file probably `routing.yml` add:

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
