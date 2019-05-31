Installation instructions
=========================

Requirements
------------

* eZ Platform 1.0+
* eZ Publish 5

Installation steps
------------------

### Use Composer

Run the following from your website root folder to install NetgenInformationCollectionBundle:

```bash
$ composer require netgen/information-collection-bundle
```

### Activate the bundle

Activate required bundles in `app/AppKernel.php` file by adding them to the `$bundles` array in `registerBundles` method:

```php
public function registerBundles()
{
    ...
    $bundles[] = new Netgen\Bundle\EzFormsBundle\NetgenEzFormsBundle();
    $bundles[] = new Netgen\Bundle\InformationCollectionBundle\NetgenInformationCollectionBundle();

    return $bundles;
}
```

### Include routing configuration

In your main routing configuration file probably `routing.yml` add:

```yaml
_netgen_information_collection:
    resource: '@NetgenInformationCollectionBundle/Resources/config/routing.yml'
```


### Enable auto_mapping for Doctrine

Add this to `config.yml`, it will make Doctrine automatically load the mappings from our bundle:

```yaml
doctrine:
    orm:
        auto_mapping: true
```


### Set siteaccess aware configuration

Here is sample configuration for actions, the developer will need to define a list of actions to be run depending on the content type.
Configuration needs to be added in `app/config/config.yml` or `app/config/ezplatform.yml`:

```yaml
netgen_information_collection:
    system:
        default:
            action_config:
                email:
                    templates:
                        default: 'AcmeBundle:email:default.html.twig'
                    default_variables:
                        sender: 'sender@example.com'
                        recipient: 'recipient@example.com'
                        subject: 'Subject'
            actions:
                default:
                    - email
                    - database
```

Don't forget to create default email template. 

### Clear the caches

Clear the eZ Publish caches with the following command:

```bash
$ php app/console cache:clear
```

For more detailed configuration, please check [documentation](DOC.md).
