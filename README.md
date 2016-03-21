# Novactive eZ Responsive Images Bundle

Novactive eZ Responsive Images is a lightweight eZ Publish 5.x|6.x bundle for Responsive Images management.


## Requirements

* eZ Publish 5.4+ / eZ Publish Community Project 2014.07+
* PHP 5.4+


##  Install

### Use Composer

Add the following to your composer.json and run `php composer.phar update novactive/ezresponsiveimagesbundle` to refresh dependencies:

```json
"require": {
    "novactive/ezresponsiveimagesbundle": "dev-master",
}
```


### Register the bundle

Activate the bundle in `app\Appkernel.php` file.

```php
// ezpublish\EzPublishKernel.php

public function registerBundles()
{
   ...
   $bundles = array(
       new FrameworkBundle(),
       ...
       new Novactive\Bundle\eZResponsiveImagesBundle\NovaeZResponsiveImagesBundle(),
   );
   ...
}
```

### Create the _mobile and _retian Alias Name

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

License
-------

[License](LICENSE)
