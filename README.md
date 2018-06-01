Novactive HTML integration bundle
==========================

Novactive HTML integration bundle is an eZ Platform bundle providing a way to do the HTML integration directly using twig templates

It use the [siteaccess](https://doc.ezplatform.com/en/latest/guide/siteaccess/) and the [design engine](https://doc.ezplatform.com/en/latest/guide/design_engine/) provided by eZ Platform.

## Features

When you access a siteaccess in the siteaccess group `html_integration`, it will take the url and show the twig template having the corresponding path.

You can configure as many siteaccess as you want, and using the design engine you can configure a different theme for each siteaccess.

As we are using the siteaccess feature, u can also use the [permissions](https://doc.ezplatform.com/en/latest/guide/permissions/) to control the access.

### Example

Considering a sitaccess `integration-test` matched by URI, the url `http://localhost/integration-test/news/details` will show the template in `Resources\views\themes\___\news\details.html.twig`

## Requirements

* eZ Platform 2+
* PHP 7.1+

## Installation

### Get the bundle

Run `composer require novactive/htmlintegrationbundle` to install the bundle and its dependencies.

### Register the bundle

Activate the bundle in `app\AppKernel.php` file.

```php
// app\AppKernel.php

public function registerBundles()
{
   ...
   $bundles = array(
       new FrameworkBundle(),
       ...
       new Novactive\Bundle\HtmlIntegrationBundle\HtmlIntegrationBundle.php(),
   );
   ...
}
```

### Configuration
Add your siteaccess and theme configuration

```yaml

ezpublish:
    siteaccess:
        list:
            - integration_1
            - integration_2
        groups:
            html_integration:
                - integration_1
                - integration_2
        match:
            Map\URI: 
                integration-1: integration_1
                integration-2: integration_2
    system:
        integration_1:
            design: integration_1_design
        integration_2:
            design: integration_2_design
ezdesign:
    design_list:
        integration_1_design: [integration_1]
        integration_2_design: [integration_2]

```

