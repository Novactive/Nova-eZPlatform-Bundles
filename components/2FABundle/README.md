# Novactive eZ 2FA Bundle

----

Novactive eZ 2FA Bundle provides two-factor authentication for your ezplatform/ibexa project.

## Installation

### Requirements

* eZ Platform 3.1+
* PHP 7.3

### Use Composer

Add the lib to your composer.json, run `composer require novactive/ez2fabundle` to refresh dependencies.

### Register the bundle

Then inject the bundle in the `config\bundles.php` of your application.

```php
    return [
        // ...
        Novactive\Bundle\eZ2FABundle\NovaeZ2FABundle::class => [ 'all'=> true ],
    ];
```

### Add routes

Make sure you add this route to your routing:

```yml
# config/routes.yaml

_novaez2fa_routes:
    resource: '@NovaeZ2FABundle/Resources/config/routing.yaml'

```

### Update Configuration

```yaml
# config/security.yaml

security:
    ...
    firewalls:
        ...
        ezpublish_front:
            pattern: ^/
            user_checker: eZ\Publish\Core\MVC\Symfony\Security\UserChecker
            anonymous: ~
            ezpublish_rest_session: ~
            form_login:
                require_previous_session: false
                csrf_token_generator: security.csrf.token_manager
            logout: ~
            two_factor:
                auth_form_path: 2fa_login    # The route name you have used in the routes.yaml
                check_path: 2fa_login_check  # The route name you have used in the routes.yaml
    
    ...
    access_control:
        - { path: ^/logout, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/2fa, role: IS_AUTHENTICATED_2FA_IN_PROGRESS }

```

### Add new configuration

The values can be updated according to the project specification

```yaml
# config/packages/scheb_two_factor.yaml

scheb_two_factor:
    google:
        enabled: true
        server_name: Local Ez Server                # Server name used in QR code
        issuer: EzIssuer                            # Issuer name used in QR code
        digits: 6                                   # Number of digits in authentication code
        window: 1                                   # How many codes before/after the current one would be accepted as valid
        template: "@ezdesign/2fa/auth.html.twig"    # Template for the 2FA login page

```

### Create the table in DB:

See the file `bundle/Resources/sql/schema.sql`