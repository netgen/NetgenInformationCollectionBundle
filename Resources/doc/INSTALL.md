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

```bash
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

### Set siteaccess aware configuration

Here is sample configuration for actions, the developer will need to define a list of actions to be run depending on the content type.
Configuration needs to be added in `app/config/config.yml` or `app/config/ezplatform.yml`:

```yaml
netgen_information_collection:
   system:
       default:
           actions:
               default:
                   - database
               content_type:
                   ng_feedback_form:
                       - email
                       - database
                   other_form:
                       - database
                   some_other_form: 
                       - email
```

Define fallback values for email, in case if content type does not have them defined:

```yaml
netgen.default.information_collection.email.recipient: 'recipient@example.com'
netgen.default.information_collection.email.subject: 'Subject'
netgen.default.information_collection.email.sender: 'sender@example.com'
```

Also define email templates for content types:

```yaml
netgen.default.information_collection.email.ng_feedback_form: 'AcmeBundle:email:ng_feedback_form.html.twig'
netgen.default.information_collection.email.some_other_form: 'AcmeBundle:email:some_other_form.html.twig'
```

And fallback email template:

```yaml
netgen.default.information_collection.email.default: 'AcmeBundle:email:default.html.twig'
```

### Clear the caches

Clear the eZ Publish caches with the following command:

```bash
$ php app/console cache:clear
```
