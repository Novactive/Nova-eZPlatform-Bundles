# Novactive eZ Maintenance Bundle

----

This repository is what we call a "subtree split": a read-only copy of one directory of the main repository. 
It is used by Composer to allow developers to depend on specific bundles.

If you want to report or contribute, you should instead open your issue on the main repository: https://github.com/Novactive/Nova-eZPlatform-Bundles

Documentation is available in this repository via `.md` files but also packaged here: https://novactive.github.io/Nova-eZPlatform-Bundles/master/MaintenanceBundle/README.md.html

----


[![Downloads](https://img.shields.io/packagist/dt/novactive/ezmaintenancebundle.svg?style=flat-square)](https://packagist.org/packages/novactive/ezmaintenance)
[![Latest version](https://img.shields.io/github/release/Novactive/NovaeZMaintenanceBundle.svg?style=flat-square)](https://github.com/Novactive/NovaeZMaintenanceBundle/releases)
[![License](https://img.shields.io/packagist/l/novactive/ezmaintenancebundle.svg?style=flat-square)](LICENSE)

Easily enable a Maintenance page.


## Installation

### Step 1: Download using composer

Add the lib to your composer.json, run `composer require novactive/ezmaintenancebundle` to refresh dependencies.

### Step 2: Enable the bundle

Then inject the bundle in the `bundles.php` of your application.

```php
     Novactive\NovaeZMaintenanceBundle\NovaeZMaintenanceBundle::class => [ 'all'=> true ],
```

### Step 3: Add the default routes

Activate the sroutes:

```yml
_novaezmaintenance_routes:
    resource: '@NovaeZMaintenanceBundle/Resources/config/routing/main.yaml'
```

### Step 4: Clear the cache and check

```bash
php app|ezpublish/console cache:clear --env=dev
```


## Configuration

```yaml
nova_ezmaintenance:
    system:
        default:
            enable: false # to enable to capability
            template: '@ezdesign/maintenance.html.twig' # the template you want as a maintenace page
            lock_file_id: 'plop.lock' # the name of the lock file in the cluster

```
