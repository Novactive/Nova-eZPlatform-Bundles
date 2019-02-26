# Novactive eZ LDAP Authenticator Bundle

A bundle to authenticate users against LDAP server

## Installation
First of all you must enable this bundle:
```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = [
        //..
        new Novactive\EzLdapAuthenticatorBundle\EzLdapAuthenticatorBundle(),
    ];
}
```
## Configuration
### Bundle configuration
To configure this bundle you should add new section `nova_ez_ldap` into your `config.yml` file.
Basic config might be like this one:

```yaml
nova_ez_ldap:
    ldap:
        connection:
            connection_string: '%ldap_connection_string%'
        search:
            search_dn: '%ldap_read_only_user%'
            search_password: '%ldap_read_only_password%'
            uid_key: uid
        base_dn: '%ldap_base_dn%'
    ez_user:
        email_attr: mail
        attributes:
            - { ldap_attr: givenName,  user_attr: first_nam }
            - { ldap_attr: sn,  user_attr: last_name }
        target_usergroup: '%target_usergroup%'
```

Instead of `connection_string` you are able to set `host`, `port`, `encryption` and `version` separately.
Also you can set `options` array that will be passed directly to the Symfony Ldap component.

#### LDAP attributes mapping
To be able to store user who came from LDAP you have to configure mapping between LDAP attributes and eZPublish user fields.
You must map all required fields.  
By default eZPublish needs user credentials and email but you may have any additional fields in you `User` content class so you should fill all of them.

#### Target group
All users will be stored in the group `target_usergroup`. You must put group object id here.

#### Full default config
Here is full default bundle configuration:
```yaml
nova_ez_ldap:
    ldap:
        connection:
            connection_string:    ''
            host:                 localhost
            port:                 389
            version:              3
            encryption:           none # One of "none"; "ssl"; "tls"
            options:              []
        search:
            search_dn:            ~ # Required
            search_password:      ~ # Required
            password_attribute:   userPassword
            uid_key:              uid
            search_string:        '({uid_key}={username})'
        base_dn:              ~ # Required
    ez_user:

        # eZPublish requres email to create user
        email_attr:           ~ # Required
        attributes:
            # Prototype
            -
                ldap_attr:        ~ # Required
                user_attr:        ~ # Required
        target_usergroup:     ~ # Required
    default_roles:
        # Default:
        - ROLE_USER
```

### Security configuration
Besides common bundle configuration you will have to add some parameters in `security` section:
```yaml
security:
    providers:
        # If you want to use not only LDAP user provider
        chain_provider:
            chain:
                providers: [nova_ldap, ezpublish]
        ezpublish:
            id: ezpublish.security.user_provider
        nova_ldap:
            id: nova_ez.ldap.user_provider
    firewalls:
        ezpublish_front:
            form_login_ldap:
                service: nova_ez.ldap
                provider: nova_ldap
                dn_string: 'o=gouv,c=fr'
                query_string: '(uid={username})'
```
