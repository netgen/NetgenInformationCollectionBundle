Symfony commands
================

The following is a list of Symfony commands available in NetgenInformationCollection.


``nginfocollector:anonymize``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This script can be used to export one or more layouts or mappings to JSON format.

To specify the type of the entity you wish to export, you need to provide it to
the script as the first argument.

To specify the ID of the entity to export, provide it to the script as the
second argument.

For example, to export the layout with ID of 1, call the script like this:

.. code-block:: shell

    $ php bin/console nglayouts:export layout 1

Or to export a mapping with an ID of 1, call the script with:

.. code-block:: shell

    $ php bin/console nglayouts:export rule 1

You can also specify the list of IDs which will then be exported together:

.. code-block:: shell

    $ php bin/console nglayouts:export layout 1,2,3

If you want to export to file, you can redirect the standard output:

    $ php bin/console nglayouts:export layout 1,2,3 > layouts.json

