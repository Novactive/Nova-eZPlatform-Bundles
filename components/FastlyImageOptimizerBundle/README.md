# Novactive eZ Fastly Image Optimizer Bundle

----

This repository is what we call a "subtree split": a read-only copy of one directory of the main repository. 
It is used by Composer to allow developers to depend on specific bundles.

If you want to report or contribute, you should instead open your issue on the main repository: https://github.com/Novactive/Nova-eZPlatform-Bundles

Documentation is available in this repository via `.md` files but also packaged here: https://novactive.github.io/Nova-eZPlatform-Bundles/master/CloudinaryBundle/README.md.html

----

[![Downloads](https://img.shields.io/packagist/dt/novactive/ezfastlyiobundle.svg?style=flat-square)](https://packagist.org/packages/novactive/ezfastlyiobundle)
[![Latest version](https://img.shields.io/github/release/Novactive/NovaeZFastlyImageOptimizerBundle.svg?style=flat-square)](https://github.com/Novactive/NovaeZFastlyImageOptimizerBundle/releases)
[![License](https://img.shields.io/packagist/l/novactive/ezfastlyiobundle.svg?style=flat-square)](LICENSE)

Novactive eZ Fastly Image Optimizer Bundle is an eZPlatform bundle for images optimizations and manipulations.

This bundle brings the power of [Fastly Image Optimization API](https://docs.fastly.com/en/guides/image-optimization-api) into your eZ Platform project.

The plugin allows you to define Fastly Image Optimization Variations on top of eZ Variations.
The image source is adapted to inject  Fastly Image Optimization parameters based on your configuration.

> All the configuration is SiteAccessAware then you can have different one depending on the SiteAccess

## INSTALL

### Use Composer

Add the lib to your composer.json, run `composer require novactive/ezfastlyiobundle` to refresh dependencies.

Then inject the bundle in the `bundles.php` of your application.

```php
    Novactive\Bundle\eZFastlyImageOptimizerBundle\NovaeZFastlyImageOptimizerBundle::class => [ 'all'=> true ],
```

## Usage

This bundle mimics the native image variation system.

```yaml
nova_ezfastlyio:
    system:
        default:
            fastlyio_variations:
                bright:
                    filters:
                        width: 200
                        brightness: -50
                        blur: 50
```

You can also provide the reference variation.
It is not recommended, using the original gives you better results on Fastly and avoid a variation to be generated on eZ side.

```yaml
nova_ezfastlyio:
    system:
        default:
            fastlyio_variations:
                ezreference_variation: medium
                bright:
                    filters:
                        width: 200
                        brightness: -50
                        blur: 50
```

> In this configuration Fastly Image Optimizer will manipulate the medium variation.


In your template

```twig
    {{ ez_render_field( content, "image",{
        "parameters": {"alias": 'simpletest2'},
        "attr" : { "class" : "img-responsive" }
    }
    ) }}
```

Automatically, `nova_ezfastlyio_alias` will be used instead of `ez_image_alias`.
The bundle fallback on the native Variation system if the alias name does not exist in `fastlyio_variations`

Then basically there is no change in your code, just yaml configuration for your Variations.

> if you have overrided the content_fields, be sure to update the call `nova_ezfastlyio_alias`


## How to manage local dev

In local you won't have Fastly Image Optimizer, the system will just add the parameters in the URI of your images.

Depending on your need to may want to disable the FastlyIO Variations.

```yaml

# config/packages/dev/variations.yaml

nova_ezfastlyio:
    system:
        site:
            fastlyio_disabled: true
```

> that's not required,=
