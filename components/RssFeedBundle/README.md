# RSS Bundle installation instructions

----

This repository is what we call a "subtree split": a read-only copy of one directory of the main repository. 
It is used by Composer to allow developers to depend on specific bundles.

If you want to report or contribute, you should instead open your issue on the main repository: https://github.com/Novactive/Nova-eZPlatform-Bundles

Documentation is available in this repository via `.md` files but also packaged here: https://novactive.github.io/Nova-eZPlatform-Bundles/master/RssFeedBundle/README.md.html

----

[![Downloads](https://img.shields.io/packagist/dt/novactive/ezrssfeedbundle.svg?style=flat-square)](https://packagist.org/packages/novactive/ezrssfeedbundle)
[![Latest version](https://img.shields.io/github/release/Novactive/NovaeZRssFeedBundle.svg?style=flat-square)](https://github.com/Novactive/NovaeZRssFeedBundle/releases)
[![License](https://img.shields.io/packagist/l/novactive/ezrssfeedbundle.svg?style=flat-square)](LICENSE)

## Installation steps

### Use Composer

Run the following from your website root folder to install Rss Bundle:

```
$ composer require novactive/ezrssfeedbundle
```

### Activate the bundle

Activate the bundle in `app/AppKernel.php` file by adding it to the `$bundles` array in `registerBundles` method, together with other required bundles:

```php
public function registerBundles()
{
    ...

     $bundles[] = new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle();
     $bundles[] = new Novactive\EzRssFeedBundle\EzRssFeedBundle();

    return $bundles;
}
```

### Edit configuration

Put the following in your `app/config/routing.yml` file to be able to display rss view pages:

```yml
EzRssFeedBundle:
    resource: '@EzRssFeedBundle/Resources/config/routing.yml'
```

If you're installing Rss Bundle on eZ Platform 2.x and plan to use it integrated into eZ Platform Admin UI, you need to add it to Assetic configuration in `app/config/config.yml`, together with `EzPlatformAdminUiBundle` and all other bundles already configured there:

```
assetic:
    bundles: [EzPlatformAdminUiBundle, EzRssFeedBundle]
```

### Import database tables

Rss Bundle uses custom database tables to store data. Use the following command to add the tables to your eZ Publish database:

```
$ php bin/console doctrine:schema:update 
```

### Clear the caches

Clear the eZ Publish caches with the following command:

```bash
$ php app/console cache:clear
```

### Install and dump assets

Run the following to correctly install and dump assets for admin UI. Make sure to use the correct Symfony environment with `--env` parameter:

```bash
$ php app/console assets:install --symlink --relative
$ php app/console assetic:dump
```

### Templating

A default view "rss_line" was created with an associated default template.
The override rule supports all types of content

If you want to implement a particular view for a content type just do it like this:

```yml
system:
    default:
        content_view:
            rss_line:
                article:
                    template: "AcmeBlogBundle:eZViews:line/article.html.twig"
                    match:
                        Identifier\ContentType: [article]
```

Note : 

I have not found any other solution for the moment, so that your rule of override is active it is necessary that your bundle is loaded last in appKernel after this bundle.                        
