# AlmaviaCX Ibexa Richtext Extra Bundle

----

This repository is what we call a "subtree split": a read-only copy of one directory of the main repository. 
It is used by Composer to allow developers to depend on specific bundles.

If you want to report or contribute, you should instead open your issue on the main repository: https://github.com/Novactive/Nova-eZPlatform-Bundles

Documentation is available in this repository via `.md` files but also packaged here: https://novactive.github.io/Nova-eZPlatform-Bundles/master/2FABundle/README.md.html

----

This bundle extend the Ibexa Richtext Editor with the following features :
- add an edit button to embed and images in the editor which allow to edit the corresponding content
- add a button to upload a file as an embed in the editor

## Installation

### Requirements

* Ibexa 4.5
* PHP 7.3 || 8.0

### Use Composer

Add the lib to your composer.json, run `composer require almaviacx/ibexarichtextextrabundle` to refresh dependencies.

### Register the bundle

Then inject the bundle in the `config\bundles.php` of your application.

```php
    return [
        // ...
        AlmaviaCX\Bundle\IbexaRichTextExtraBundle\AlmaviaCXIbexaRichTextExtraBundle::class => [ 'all'=> true ],
    ];
```

### Add routes

Make sure you add this route to your routing:

```yaml
# config/routes.yaml

_almaviacx_ibexa_rich_text_extra_bundle_routes:
    resource: '@AlmaviaCXIbexaRichTextExtraBundle/Resources/config/routing.yaml'
```

### Configuration

This bundle define the following setting which allow tu customize the file upload behavior

```yaml
# Binary files mappings
ibexa.site_access.config.default.fieldtypes.binaryfile.mappings:
    content_type_identifier: file
    content_field_identifier: file
    name_field_identifier: name
    parent_location_id: 52
    mime_types:
        - image/svg+xml
        - application/msword
        - application/vnd.openxmlformats-officedocument.wordprocessingml.document
        - application/vnd.ms-excel
        - application/vnd.openxmlformats-officedocument.spreadsheetml.sheet
        - application/vnd.ms-powerpoint
        - application/vnd.openxmlformats-officedocument.presentationml.presentation
        - application/pdf
```
