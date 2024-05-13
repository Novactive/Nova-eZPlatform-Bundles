# NovaeZEnhancedImageAssetBundle

----

This repository is what we call a "subtree split": a read-only copy of one directory of the main repository. 
It is used by Composer to allow developers to depend on specific bundles.

If you want to report or contribute, you should instead open your issue on the main repository: https://github.com/Novactive/Nova-eZPlatform-Bundles

Documentation is available in this repository via `.md` files but also packaged here: https://novactive.github.io/Nova-eZPlatform-Bundles/master/EnhancedImageAssetBundle/README.md.html

----

[![Downloads](https://img.shields.io/packagist/dt/novactive/ezenhancedimageassetbundle.svg?style=flat-square)](https://packagist.org/packages/novactive/ezenhancedimageassetbundle)
[![Latest version](https://img.shields.io/github/release/Novactive/NovaeZEnhancedImageAssetBundle.svg?style=flat-square)](https://github.com/Novactive/NovaeZEnhancedImageAssetBundle/releases)
[![License](https://img.shields.io/packagist/l/novactive/ezenhancedimageassetbundle.svg?style=flat-square)](LICENSE)

An eZPlatform bundle providing new field type with enhanced features related to image management

## Features

- [Focus point managment in admin UI](#1-focus-point)
- [Default image configuration for file size optimization](#2-default-image-configuration)
- [Lazy and Progressive loading](#3-lazyloading)
- [Retina variations](#4-retina-variations)
- [WebP variations](#5-webp-variations)
- [Twig render field parameters](#6-twig-render-field-parameters)

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

### 1. Focus point

This bundle provide a new `enhancedimage` field type which extend the `ezimage` field type.
This field type allow the user to select a focus point on the uploaded image.
Variation can then be created based on the selected focus point.

![Demo](doc/images/image-focus-demo.gif)

[Check out the demo](https://image-focus.stackblitz.io/)

### 2. Default image configuration

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

### 3. LazyLoading
Lazy loading is controlled globaly by the following settings (default to true) and can be overriden at field level.

```yaml
parameters: 
  ez_enhanced_image_asset.default.enable_lazy_load: true
```

### 4. Retina variations 

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

### 5. WebP variations

Auto-creation of a webp variation for each existing variations and will then automaticaly add this variation as an alternative source

### 6. Twig render field parameters

You can now specify the `alternativeAlias` parameter to define alternative image alias depending the screen size

```twig

{{ ibexa_render_field(content, fieldIdentifier, {
    parameters: {
        alias: 'desktop_alias',
        alternativeAlias: [
            {
                alias: 'mobile_alias',
                media: '(max-width: 320px)'
            }
        ],
        lazyLoad: true|false, // optionnal
        retina: true|false // optionnal
    }
}) }}
```

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
{{ encore_entry_link_tags('enhancedimage-css', null, 'ibexa') }}
```
 
```twig
{{ encore_entry_script_tags('enhancedimage-js', null, 'ibexa') }}
```







