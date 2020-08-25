# EditHelpBundle

----

This repository is what we call a "subtree split": a read-only copy of one directory of the main repository. 
It is used by Composer to allow developers to depend on specific bundles.

If you want to report or contribute, you should instead open your issue on the main repository: https://github.com/Novactive/Nova-eZPlatform-Bundles

Documentation is available in this repository via `.md` files but also packaged here: https://novactive.github.io/Nova-eZPlatform-Bundles/master/CloudinaryBundle/README.md.html

----

[![Downloads](https://img.shields.io/packagist/dt/novactive/ezedithelpbundle.svg?style=flat-square)](https://packagist.org/packages/novactive/ezedithelpbundle)
[![Latest version](https://img.shields.io/github/release/Novactive/NovaeZEditHelpBundle.svg?style=flat-square)](https://github.com/Novactive/NovaeZEditHelpBundle/releases)
[![License](https://img.shields.io/packagist/l/novactive/ezedithelpbundle.svg?style=flat-square)](LICENSE)


Add a helper when editing or creating a Content. This helper allows you to display personalised text for the Content creation/edition.

This bundle actually uses the eZ Content Repository to self-document the Content form!

##  Install

Add the lib to your composer.json, run `composer require novactive/ezedithelpbundle` to refresh dependencies.

Then inject the bundle in the `bundles.php` of your application.

```php
    Novactive\Bundle\eZResponsiveImagesBundle\NovaeZResponsiveImagesBundle::class => [ 'all'=> true ],
```

Create the Nova eZ Help Tooltip content type:

```bash
bin/console novaezhelptooltip:create
``` 

## Usage

Just create a sub tree of `Nova eZ Help Tooltip` anywhere in your Content Tree. (Media is probably recommanded).
And start describing your Content Type:

![tree]

> Just follow your Content Type logic, Content > Field 


Then the result is a alert message on the form page!

![example]

That's eZ helping eZ using eZ!


[tree]: bundle/Resources/doc/tree.png
[example]: bundle/Resources/doc/result.png

