# NovaeZEnhancedImageAssetBundle

An eZPlatform bundle providing new field type with enhanced features related to image management

## Features

- Focus point managment in admin UI
- Lazy loading
- Responsive loading
- Progressive loading

## Requirements

- eZ Platform Admin UI
- PHP 7.1+

## Installation

### Use Composer

Add NovaeZEnhancedImageAssetBundle in your composer.json:

```bash
composer require novactive/ezenhancedimageassetbundle
```

### Register the bundle

Register the bundle in your application's kernel class:

```php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = array(
        // ...
        new Novactive\EzEnhancedImageAssetBundle\EzEnhancedImageAssetBundle(),
        // ...
    );
}
```
## Usage
### 1. Include javascript and CSS

```twig
<link rel="stylesheet" href="{{ asset("bundles/ezenhancedimageasset/css/enhancedimage.css") }}" />
```
 
```twig
<script src="{{ asset("bundles/ezenhancedimageasset/js/enhancedimage.js") }}"></script>
```
#### LazyLoading
Lazy loading is controlled globaly by the following settings (default to true) and can be overriden at field level.

```yaml
parameters: 
  ez_enhanced_image_asset.default.enable_lazy_load: true
```

### 2. Image variations configuration

If needed, update your configuration and add the following filter to generate thumbnail based on the contributed focus point 
```yaml
 - { name: focusedThumbnail, params: {size: [<width>, <height>], focus: [0, 0]} }
```
NB: the `focus` parameter automaticaly updated for each image based on what has been contributed

### 3. Twig render field configuration

You can now specify the `alternativeAlias` parameter to define alternative image alias depending the screen size

```twig

{{ ez_render_field(content, fieldIdentifier, {
    parameters: {
        alias: 'desktop_alias',
        alternativeAlias: [
            {
                alias: 'mobile_alias',
                media: '(max-width: 320px)'
            }
        ]
        lazyLoad: true|false // optionnal
    }
}) }}
```

## Migrate existing ezimage fields

As the new `enhancedimage` field type is an extend of the `ezimage` field type, you just need to update the `data_type_string` column in the database for the fields you want.

Example :
```sql
UPDATE ezcontentclass_attribute ca
INNER JOIN ezcontentclass c ON c.id = ca.contentclass_id
SET data_type_string = "enhancedimage"
WHERE ca.data_type_string = "ezimage" AND c.identifier="my_content_type" AND ca.identifier="my_field_identifier";

UPDATE ezcontentobject_attribute oa
INNER JOIN ezcontentclass_attribute ca ON oa.contentclassattribute_id = ca.id
SET oa.data_type_string = "enhancedimage"
WHERE oa.data_type_string = "ezimage" AND ca.data_type_string="enhancedimage";
```
