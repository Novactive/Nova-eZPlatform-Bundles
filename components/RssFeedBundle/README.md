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

Add the lib to your composer.json, run `composer require novactive/ezrssfeedbundle` to refresh dependencies.

Then inject the bundle in the `bundles.php` of your application.

```php
   Novactive\EzRssFeedBundle\EzRssFeedBundle::class => [ 'all'=> true ],
```

### Add routes

Make sure you add this route to your routing:

```yml
# config/routes.yaml

EzRssFeedBundle:
    resource: '@EzRssFeedBundle/Resources/config/routing.yml'
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
```

### Templating

A default view "rss_line" was created with an associated default template.
The override rule supports all types of content items.

If you want to implement a particular view for a content type just do it like this:

```yaml
system:
    default:
        content_view:
            rss_line:
                article:
                    template: "AcmeBlogBundle:eZViews:line/article.html.twig"
                    match:
                        Identifier\ContentType: [article]
```

To render meta link tag into your page head :
```
{{ render(controller('Novactive\\\EzRssFeedBundle\\Controller\\RssFeedViewController::rssHeadLinkTagsAction'))
}}
```
### Custom SiteListService

As default `Novactive\EzRssFeedBundle\Services\SiteListService` is implimented to fetch for Site Access list within Site Factory
To do your own implimenation you have to impliment the given Interface :
`Novactive\EzRssFeedBundle\Services\SiteListInterface` then config your service as following :
```yaml
Services:
  Novactive\EzRssFeedBundle\Services\SiteListServiceInterface: '@your_own.service_alias'
```

### Site label Translation
You can add site accesses translations with `novarss_sites` translation domain as following :
Inside your translation locale file (example novarss_sites.fr.yaml) 
Note : This translation is enabled using the default SiteListService
```yaml
site_access_identifier: My site
```