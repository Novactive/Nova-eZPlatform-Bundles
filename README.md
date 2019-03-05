# Novactive eZ Protected Content Bundle

A bundle that provides quick password protection on Contents.

# Installation

### Requirements

* eZ Platform 2+
* PHP 7.1+
* MySQL 5.7.8+ / Maria DB 10.1+

### Installation steps

Run `composer require novactive/ezprotectedcontentbundle` to install the bundle and its dependencies:

### Register the bundles

Activate the bundle in `app\AppKernel.php` file.

```php
// app\AppKernel.php

public function registerBundles()
{
   ...
   $bundles = array(
        new FrameworkBundle(),
        ...
        // Novactive eZ Protected Content Bundle
        new Novactive\Bundle\eZProtectedContentBundle\NovaeZProtectedContentBundle()
   );
   ...
}
```

### Add routes

```yaml
_novaezprotectedcontent_routes:
    resource: '@NovaeZProtectedContentBundle/Resources/config/routing/main.yml'
```

### Install the database schema

```bash
bin/console novaezprotectedcontent:install
```

Contributing
----------------

[Contributing](CONTRIBUTING.md)


Change and License
------------------

[License](LICENSE)


----
Made with <3 by novactive.
