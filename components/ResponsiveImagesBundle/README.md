# Novactive eZ Responsive Images Bundle

----

This repository is what we call a "subtree split": a read-only copy of one directory of the main repository. 
It is used by Composer to allow developers to depend on specific bundles.

If you want to report or contribute, you should instead open your issue on the main repository: https://github.com/Novactive/Nova-eZPlatform-Bundles

Documentation is available in this repository via `.md` files but also packaged here: https://novactive.github.io/Nova-eZPlatform-Bundles/master/ResponsiveImagesBundle/README.md.html

----

[![Downloads](https://img.shields.io/packagist/dt/novactive/ezresponsiveimagesbundle.svg?style=flat-square)](https://packagist.org/packages/novactive/ezresponsiveimagesbundle)
[![Latest version](https://img.shields.io/github/release/Novactive/NovaeZResponsiveImagesBundle.svg?style=flat-square)](https://github.com/Novactive/NovaeZResponsiveImagesBundle/releases)
[![License](https://img.shields.io/packagist/l/novactive/ezresponsiveimagesbundle.svg?style=flat-square)](LICENSE)


Novactive eZ Responsive Images is a lightweight eZ Publish 5.x|6.x bundle for Responsive Images management.

##  Install

### Usage and main feature

Add the lib to your composer.json, run `composer require novactive/ezresponsiveimagesbundle` to refresh dependencies.

By default this bundle will use [picturefill](https://github.com/scottjehl/picturefill) to load the good version of the your variations.

You can also decide to lazy load the images, in this case the bundle uses [unveil.js](https://github.com/luis-almeida/unveil) to load the good version of the variation alias name.

Today it handles 3 versions:

* Mobile: viewport width < 640px
* Desktop: default choice
* Retina: devicePixelRatio > 1,

Then it is really interesting to understand that only the good version will be loaded in the browser.
The Lazy loading is based on the view port too, if an image is not visible on the screen it will be pre-loaded and loaded on the scroll action.

It means:

* it reduces drastically the size and the load time of the page by not loading the non visible images
* when an image is loaded, it ensures that is the adapted one.


```twig
        {{ ez_render_field(content, 'image', {
            parameters: {
                alias: 'blog_post_line_home',
                class: 'img-responsive img-rounded',
                unveiled: true,
            }
        }) }}
```

> Unveiled means "Lazy Loading"

> Read below, you will need 2 more aliases for each current alias that you have.

### Use Composer

Add the lib to your composer.json, run `composer require novactive/ezprotectedcontentbundle` to refresh dependencies.

Then inject the bundle in the `bundles.php` of your application.

```php
    Novactive\Bundle\eZResponsiveImagesBundle\NovaeZResponsiveImagesBundle::class => [ 'all'=> true ],
```

### Create the _mobile and _retina Alias Name

The bundle requires that you create 2 more alias for each alias you are using. Ex:

```yaml
    gallery_full_thumbnail:
        reference: ~
        filters:
            - { name: geometry/scaledownonly, params: [354, 224] }

    gallery_full_thumbnail_mobile:
        reference: gallery_full_thumbnail
        filters:
            - { name: geometry/scalewidthdownonly, params: [175] }

    gallery_full_thumbnail_retina:
        reference: ~
        filters:
            - { name: geometry/scaledownonly, params: [708, 448] }
```


### Load the resources in your pagelayout

```twig
    <head>
        ...
        {% include 'NovaeZResponsiveImagesBundle::novaezresponsiveimages.html.twig' %}
    </head>
```

