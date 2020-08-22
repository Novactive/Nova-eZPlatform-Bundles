# Novactive eZ Protected Content Bundle

----

This repository is what we call a "subtree split": a read-only copy of one directory of the main repository. 
It is used by Composer to allow developers to depend on specific bundles.

If you want to report or contribute, you should instead open your issue on the main repository: https://github.com/Novactive/Nova-eZPlatform-Bundles

Documentation is available in this repository via `.md` files but also packaged here: https://novactive.github.io/Nova-eZPlatform-Bundles/master/ProtectedContentBundle/README.md.html

----

[![Downloads](https://img.shields.io/packagist/dt/novactive/ezprotectedcontentbundle.svg?style=flat-square)](https://packagist.org/packages/novactive/ezprotectedcontentbundle)
[![Latest version](https://img.shields.io/github/release/Novactive/NovaeZProtectedContentBundle.svg?style=flat-square)](https://github.com/Novactive/NovaeZProtectedContentBundle/releases)
[![License](https://img.shields.io/packagist/l/novactive/ezprotectedcontentbundle.svg?style=flat-square)](LICENSE)

A bundle that provides quick password protection on Contents.

## How it works

Allows you to add 1 on N password on a Content in the Admin UI.

Once a Password is set, the Content becomes Protected. In this situation you will have 2 new variables in the view full.
Allowing you do:

```twig
<h2>{{ ez_content_name(content) }}</h2>
{% if not canReadProtectedContent %}
    <p>This content has been protected by a password</p>
    <div class="protected-content-form">
        {{ form(requestProtectedContentPasswordForm) }}
    </div>
{% else %}
    {% for field in content.fieldsByLanguage(language|default(null)) %}
        <h3>{{ field.fieldDefIdentifier }}</h3>
        {{ ez_render_field(content, field.fieldDefIdentifier) }}
    {% endfor %}
{% endif %}
```

You can also manage this globally through the pagelayout wrapping the content block.

Once you have unlocked the content, __canReadProtectedContent__ will be __true__ 

> HTTP Cache is disabled for Protected Content.


## Installation

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

### Varnish

This module add a cookie to unlock the contents that match it, for that reason you want to keep all the cookie that 
starts with PasswordProvided::COOKIE_PREFIX (i.e: **protected-content-**).

```vcl
 // Remove all cookies besides Session ID, as JS tracker cookies and so will make the responses effectively un-cached
    if (req.http.cookie) {
        set req.http.cookie = ";" + req.http.cookie;
        set req.http.cookie = regsuball(req.http.cookie, "; +", ";");
        set req.http.cookie = regsuball(req.http.cookie, ";[ ]*(eZSESSID[^=]*|protected-content-[^=]*)=", "; \1=");
        set req.http.cookie = regsuball(req.http.cookie, ";[^ ][^;]*", "");
        set req.http.cookie = regsuball(req.http.cookie, "^[; ]+|[; ]+$", "");
    }
```

