NetgenInformationCollectionBundle installation instructions
===========================================================

Requirements
------------

* eZ Platform 1.0+
* eZ Publish 5

Installation steps
------------------

### Use Composer

Run the following from your website root folder to install Netgen InformationCollection Bundle:

```
$ composer require netgen/information-collection-bundle
```

### Activate the bundle

Activate the bundle in `app/AppKernel.php` file by adding it to the `$bundles` array in `registerBundles` method:

```php
public function registerBundles()
{
    ...
    $bundles[] = new Netgen\Bundle\InformationCollectionBundle\NetgenInformationCollectionBundle();

    return $bundles;
}
```

### Clear the caches

Clear the eZ Publish caches with the following command:

```bash
$ php app/console cache:clear
```
