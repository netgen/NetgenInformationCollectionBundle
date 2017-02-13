Documentation
=============

## How it works

Information collection renders form based on content type fields. In order to completely utilize the power of this bundle we to add
some fields to content type. It must contain following:
* fields marked as **information collectors** (mandatory)
* email field with identifier *recipient*
* email field with identifier *sender*
* text field with identifier *subject*
* some other fields with success text or something else (those are not mandatory)

In case when content type does not contain recipient, sender or subject, bundle by default will use values specified in yaml configuration (*default_variables* tree). 
Then define content view for given content type (*content_view.yml*) specifiying *netgen_information_collection.controller:displayAndHandle* as controller:
```yaml
ezpublish:
    system:
        frontend_group:
            content_view:
                full:
                    my_content_type_with_information_collectors:
                        template: 'AcmeBundle:content/full:my_content_type.html.twig'
                        controller: 'netgen_information_collection.controller:displayAndHandle'
                        match:
                            Identifier\ContentType: my_content_type
```

Controller will return standard eZ ContentView with additional two variables *form* and *is_valid*. *form* contains Symfony form and it is up
to you it will be rendered and *is_valid* signals when form is valid and submitted.

Example template:
```twig
{% extends 'AcmeBundle::pagelayout.html.twig' %}

{% block content %}

    <div class="my-content-type clearfix">

        {% if is_valid is defined and is_valid %}
            <div class="confirmation-message">
                {% if not ez_is_field_empty( content, "success_text" ) %}
                    <div class="att-success-text">
                        {{ ez_render_field( content, "success_text" ) }}
                    </div>
                {% endif %}
            </div>
        {% else %}
        
            <h1 class="page-header">{{ ez_render_field( content, "title" ) }}</h1>
            
            {% if not ez_is_field_empty( content, "full_intro" ) %}
                <div class="intro">
                    {{ ez_render_field( content, "full_intro" ) }}
                </div>
            {% endif %}
    
            {% if not ez_is_field_empty( content, "body" ) %}
                <div class="body">
                    {{ ez_render_field( content, "body" ) }}
                </div>
            {% endif %}
                
            {{ form_start(form, { 'attr': { 'class': 'contact-us form-default' } } ) }}
            {{ form_errors(form) }}
    
            <div class="form-group">
                <div class="control-label">
                    {{ form_label( form.first_name ) }}:
                </div>
                {{ form_widget( form.first_name, {'attr': {'class': 'form-control'} } ) }}
                {{ form_errors( form.first_name ) }}
            </div>
    
            <div class="form-group">
                <div class="control-label">
                    {{ form_label( form.last_name ) }}:
                </div>
                {{ form_widget( form.last_name, {'attr': {'class': 'form-control'} } ) }}
                {{ form_errors( form.last_name ) }}
            </div>
    
            <div class="form-group">
                <div class="control-label">
                    {{ form_label( form.email ) }}:
                </div>
                {{ form_widget( form.email, {'attr': {'class': 'form-control'} } ) }}
                {{ form_errors( form.email ) }}
            </div>
    
            <div class="form-group">
                <div class="control-label">
                    {{ form_label( form.comment ) }}:
                </div>
                {{ form_widget( form.comment, {'attr': {'class': 'form-control'} } ) }}
                {{ form_errors(form.comment) }}
            </div>
    
            <div class="form-group button-area">
                <button type="submit" class="btn btn-primary">Send</button>
            </div>
    
            {{ form_rest(form) }}
            {{ form_end(form) }}

        {% endif %}

    </div>

{% endblock %}
```

## Configuration

For advanced configuration documentation and examples please check [documentation](CONFIGURATION.md).

## Actions

What is action ? Action defines what needs to be done when information collection for is submitted. 
For every content type action list must be defines. When form is submitted, handler travers over list and executes actions.
 
[Actions](ACTIONS.md)

## Field handlers

By default every field value is transformed (cast) to string, if end developer needs customized string of some field value
then custom field value handler must be implemented.

[Field handlers](FIELD_HANDLERS.md)


