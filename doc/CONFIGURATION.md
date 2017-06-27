Configuration
=============

Some advanced use cases.

## Specifiying which action to run per content type

By default all actions set by *default* under *actions* tree will be executed for all content types.
 
```yaml
actions:
    default:
        - email
        - database
```

To run only specific action for given content type, for example *my_content_type* and *email* add this to configuration:

```yaml
actions:
    default:
        - email
        - database
    content_types:
        my_content_type:
            - email
```

Or to execute only *database* action for *my_content_type_2*:

```yaml
actions:
    default:
        - email
        - database
    content_types:
        my_content_type:
            - email
        my_content_type_2:
            - database
```

## Specifiying which email templates to use per content type

In case when want to split email templates per content type, rather than using default one all, add this to configuration:

```yaml
action_config:
    email:
        templates:
            default: 'AcmeBundle:email:default.html.twig'
            content_types:
                my_content_type: 'AcmeBundle:email:my_content_type.html.twig'
                my_content_type2: 'AcmeBundle:email:my_content_type2.html.twig'
```
