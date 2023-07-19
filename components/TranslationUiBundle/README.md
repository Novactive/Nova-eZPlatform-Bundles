# AlmaviaCX Ibexa Translation UI Bundle

----

This repository is what we call a "subtree split": a read-only copy of one directory of the main repository. 
It is used by Composer to allow developers to depend on specific bundles.

If you want to report or contribute, you should instead open your issue on the main repository: https://github.com/Novactive/Nova-eZPlatform-Bundles

Documentation is available in this repository via `.md` files but also packaged here: https://novactive.github.io/Nova-eZPlatform-Bundles/master/2FABundle/README.md.html

----

This bundle integrate the UI provided by https://github.com/lexik/LexikTranslationBundle/tree/v6.0 into the Ibexa Admin UI.

## Installation

### Requirements

* Ibexa 4
* PHP 7.3 || 8.0

### Use Composer

Add the lib to your composer.json, run `composer require almaviacx/ibexatranslationuibundle` to refresh dependencies.

### Register the bundle

Then inject the bundle in the `config\bundles.php` of your application.

```php
    return [
        // ...
        Lexik\Bundle\TranslationBundle\LexikTranslationBundle::class => ['all' => true],
        AlmaviaCX\Bundle\IbexaTranslationUiBundle\AlmaviaCXIbexaTranslationUiBundle::class => [ 'all'=> true ],
    ];
```

### Add routes

Make sure you add this route to your routing:

```yaml
# config/routes.yaml

lexik_translation_edition:
  resource: "@LexikTranslationBundle/Resources/config/routing.yml"
  prefix:   /translations-ui

```

### Configuration

Follow the lexik translation bundle documentation : https://github.com/lexik/LexikTranslationBundle/blob/v6.0/Resources/doc/index.md
