# AlmaviaCX CaptchEtat Bundle

----

This repository is what we call a "subtree split": a read-only copy of one directory of the main repository.
It is used by Composer to allow developers to depend on specific bundles.

If you want to report or contribute, you should instead open your issue on the main repository: https://github.com/Novactive/Nova-eZPlatform-Bundles

Documentation is available in this repository via `.md` files but also packaged here: https://novactive.github.io/Nova-eZPlatform-Bundles/master/2FABundle/README.md.html

----

This bundle provide a form type to use CaptchEtat (https://api.gouv.fr/les-api/api-captchetat) on your website

## Installation

### Requirements

* Ibexa 4
* PHP 7.4 || 8.0

### Use Composer

Add the lib to your composer.json, run `composer require almaviacx/captchetatbundle` to refresh dependencies.

### Register the bundle

Then inject the bundle in the `config\bundles.php` of your application.

```php
    return [
        // ...
        AlmaviaCX\Bundle\CaptchEtatBundle\AlmaviaCXCaptchEtatBundle::class => [ 'all'=> true ],
    ];
```

### Add routes

Make sure you add this route to your routing:

```yaml
# config/routes.yaml

captchetat_routes:
    resource: '@AlmaviaCXCaptchEtatBundle/Resources/config/routes.yaml'
```

### Accessibility

For accessibility, you might want to add the following script to your JS

```javascript
import CaptchaEtat from '../public/bundles/almaviacxcaptchetat/js/captchetat-widget'
CaptchaEtat.init()
```

## Configuration

Configuration can be done throught the following environment variables

```
CAPTCHETAT_API_URL="https://sandbox-api.piste.gouv.fr"
CAPTCHETAT_OAUTH_URL="https://sandbox-oauth.piste.gouv.fr"
CAPTCHETAT_OAUTH_CLIENT_ID=~
CAPTCHETAT_OAUTH_CLIENT_SECRET=~
CAPTCHETAT_TIMEOUT="2.5"
CAPTCHETAT_TYPE=""
```

Depending on if you use "sandbox" (default) or "production" environment, you might want to change the urls to :
```
CAPTCHETAT_API_URL="https://api.piste.gouv.fr"
CAPTCHETAT_OAUTH_URL="https://oauth.piste.gouv.fr"
```

## Add captcha to your form 

```injectablephp
$builder->add(
    'captcha', CaptchEtatType::class, 
    [
        'label' => 'customform.show.captcha',
    ]
);
```

## Formbuilder forms
You can autommaticaly add the captcha to formbuilder forms by activating the following service decorator :

```yaml
AlmaviaCX\Bundle\CaptchEtat\FormBuilder\FieldType\Field\Mapper\ButtonFieldMapperDecorator:
    decorates: Ibexa\FormBuilder\FieldType\Field\Mapper\ButtonFieldMapper
    arguments:
        $buttonFieldMapper: '@.inner'
```
