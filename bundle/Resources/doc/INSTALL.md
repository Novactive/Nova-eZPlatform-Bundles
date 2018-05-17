# Installation

## Requirements

* eZ Platform 2+
* PHP 7.1+
* MySQL 5.7.8+ / Maria DB 10.2.4+

## Installation steps

Run `composer require novactive/ezmailingbundle` to install the bundle and its dependencies:

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
       // NovaeZMailingBundle
       new Novactive\Bundle\eZMailingBundle\NovaeZMailingBundle(),
   );
   ...
}
```

### Add routes

```yaml
_novaezmailing_routes:
    resource: '@NovaeZMailingBundle/Resources/config/routing.yml'
```

### Add configuration

You need to declare a template for the view `novaezmailingfull`

```yaml
ezpublish:
    system:
        default:
            content_view:
                novaezmailingfull:
                    folder:
                        template: yourtemplatepath
                        match:
                            Identifier\ContentType: [a_content_type]
```

> Adapt according to your configuration


You also need 2 mailers, 1 in charge to send the Mailings, the other to send the service emails.

```yaml
nova_ezmailing:
    system:
        default:
            simple_mailer: "swiftmailer.mailer.local_mailer"
            mailing_mailer: "swiftmailer.mailer.remote_mailer"
            # Default email values
            email_subject_prefix: "[NovaeZMailing]"
            email_from_address: "no-reply@novactive.com"
            email_from_name: "Novactive"
            email_return_path: "return-path@novactive.com"
```

Example in dev mode

```yaml
swiftmailer:
    default_mailer: myfirst_mailer
    mailers:
        myfirst_mailer:
            transport: 'smtp'
            host: 127.0.0.1
            port: 1025
            spool: { type: memory }
        mysecond_mailer:
            transport: 'smtp'
            host: 127.0.0.1
            port: 1025
            spool: { type: memory }

nova_ezmailing:
    system:
        default:
            simple_mailer: "swiftmailer.mailer.myfirst_mailer"
            mailing_mailer: "swiftmailer.mailer.mysecond_mailer"
```

