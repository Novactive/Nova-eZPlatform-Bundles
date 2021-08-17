# Novactive eZ 2FA Bundle

----

This repository is what we call a "subtree split": a read-only copy of one directory of the main repository. 
It is used by Composer to allow developers to depend on specific bundles.

If you want to report or contribute, you should instead open your issue on the main repository: https://github.com/Novactive/Nova-eZPlatform-Bundles

Documentation is available in this repository via `.md` files but also packaged here: https://novactive.github.io/Nova-eZPlatform-Bundles/master/2FABundle/README.md.html

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

```yaml
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

    # TOTP Authenticator config
    totp:
        enabled: true                               # If TOTP authentication should be enabled, default false
        server_name: Server Name                    # Server name used in QR code
        issuer: TOTP Issuer                         # Issuer name used in QR code
        window: 1                                   # How many codes before/after the current one would be accepted as valid
        template: "@ezdesign/2fa/auth.html.twig"    # Template used to render the authentication form

    # Trusted device feature
    trusted_device:
        enabled: true                                   # If the trusted device feature should be enabled
        # manager: acme.custom_trusted_device_manager   # Use a custom trusted device manager
        lifetime: 259200                                # Lifetime of the trusted device token, in seconds
        extend_lifetime: false                          # Automatically extend lifetime of the trusted cookie on re-login
        cookie_name: trusted_device                     # Name of the trusted device cookie
        cookie_secure: true                             # Set the 'Secure' (HTTPS Only) flag on the trusted device cookie
        cookie_same_site: "lax"                         # The same-site option of the cookie, can be "lax", "strict" or null
        # cookie_domain: ""                             # Domain to use when setting the cookie, fallback to the request domain if not set
        cookie_path: "/"                                # Path to use when setting the cookie

```

For full **scheb_two_factor** reference visit the following resource: https://github.com/scheb/two-factor-bundle/blob/4.x/Resources/doc/configuration.md


```yaml
# config/packages/nova_ez2fa.yaml

nova_ez2fa:
    system:
        # Available methods - google, totp, microsoft.
        # If microsoft is selected the totp mechanism is still used but the config is forced and static so Microsoft Authenticator app can be used.
        default:
            2fa_method: google
        site:
            2fa_method: totp
            # if microsoft method set - the config is forced to: algorithm: sha1, period: 30, digits: 6
            config:
                algorithm: sha1 #(md5, sha1, sha256, sha512)
                period: 30
                digits: 6

```

### Create the table in DB:

See the file `bundle/Resources/sql/schema.sql`


### Manually removing 2FA record for specific User:

If some User needs its 2FA record in the database removed to be able to login without entering 2FA code run the following command `acx:users:remove-2fa` with specifying user's login:

```shell script

php ezplatform/bin/console acx:users:remove-2fa user_login

```

> **Note to keep in mind**: If you have the 2FA already set up for the user and you're going to reset it by following the corresponding link on the 2FA Setup page don't change the method for the current site access before that! Because in this case the secret key will supposed to be removed for the new method not for the old one and hence the reset won't work!