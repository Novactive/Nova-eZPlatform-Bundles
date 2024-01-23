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
## Features

### 1. Default image configuration

Added to siteaccess aware parameters which allow to define the default post processors and configuration to use when generating image alias
```yaml
parameters:
  ez_enhanced_image_asset.default.image_default_post_processors: 
    pngquant:
        quality: '40-85'
    jpegoptim:
        strip_all: true
        max: 70
        progressive: true
  ez_enhanced_image_asset.default.image_default_config: 
    animated: true
    quality: 80
```

### 2. LazyLoading
Lazy loading is controlled globaly by the following settings (default to true) and can be overriden at field level.

```yaml
parameters: 
  ez_enhanced_image_asset.default.enable_lazy_load: true
```

#### 3. Retina variations 

Retina variations should suffixed by `_retina` will be used automatically (if enabled) when using the provided field template.
To be displayed, the generated variation width should be two time the width of the default variation.

Considering a variation named `my_alias`, the variation named `my_alias_retina` will be used and displayed on retina screens.
```yaml
 - { name: my_alias, params: {size: [<width>, <height>], focus: [0, 0]} }
 - { name: my_alias_retina, params: {size: [<width*2>, <height*2>], focus: [0, 0]} }
```

The automatic use of retina variations is controlled by this setting
```yaml
parameters: 
  ez_enhanced_image_asset.default.enable_retina: true
```

### 4. Twig render field parameters

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
        retina: true|false // optionnal
    }
}) }}
```

### 4. Focus point

This bundle provide a new `enhancedimage` field type which extend the `ezimage` field type. 
This field type allow the user to select a focus point on the uploaded image.
Variation can then be created based on the selected focus point.

![Demo](doc/images/image-focus-demo.gif)

[Check out the demo](https://image-focus.stackblitz.io/)

#### Variations configuration

Focused variations require the use of a "focusedThumbnail" filter to generate thumbnail based on the contributed focus point.
```yaml
 - { name: focusedThumbnail, params: {size: [<width>, <height>], focus: [0, 0]} }
```
NB: the `focus` parameter is automaticaly updated for each image based on what has been contributed

#### Migrate existing ezimage fields

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

## Usage
Some feature will require the following assets
```twig
<link rel="stylesheet" href="{{ asset("bundles/ezenhancedimageasset/css/enhancedimage.css") }}" />
```
 
```twig
<script src="{{ asset("bundles/ezenhancedimageasset/js/enhancedimage.js") }}"></script>
```







