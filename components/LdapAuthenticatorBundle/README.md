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
        new Novactive\Bundle\eZLDAPAuthenticatorBundle\EzLdapAuthenticatorBundle(),
    ];
}
```
## Configuration
### Bundle configuration
To configure this bundle you should add new section `nova_ez_ldap` into your `config.yml` file.
Basic config might be like this one:

```yaml
nova_ez_ldap:
  connections:
    default:
      ldap:
        adapter:
          connection_string: '%ldap_connection_string%'
        user_provider:
          base_dn: '%ldap_base_dn%'
          search_dn: '%ldap_read_only_user%'
          search_password: '%ldap_read_only_password%'
          uid_key:              uid
      ezuser:
        admin_user_id:  '%admin_user_id%'
        user_group_id:  '%target_usergroup%'
        email_attr: mail
        attributes:
          first_name: givenName
          last_name: sn
```

Instead of `connection_string` you are able to set `host`, `port`, `encryption` and `version` separately.
Also you can set `options` array that will be passed directly to the Symfony Ldap component.

#### LDAP attributes mapping
To be able to store user who came from LDAP you have to configure mapping between LDAP attributes and eZPublish user fields.
You must map all required fields in the `ezuser` part.
By default eZPublish needs user credentials and email but you may have any additional fields in you `User` content class so you should fill all of them.

#### Target group
All users will be stored in the group `user_group_id`. You must put group content id here.

#### Full default config
Here is full default bundle configuration:
```yaml
nova_ez_ldap:
  connections:
    default:
      ldap:
        adapter:
          connection_string: ~
          host: localhost
          port: 389
          version: 3
          encryption: none # One of "none"; "ssl"; "tls"
          options: []

        user_provider:
          base_dn: ~ # Required
          search_dn: ~ # Required
          search_password: ~ # Required
          uid_key: uid
          filter: '({uid_key}={username})'

      ezuser:
        admin_user_id:  ~ # Required
        user_group_id:  ~ # Required
        email_attr:  ~ # Required
        attributes:
          user_attr: ldap_attr
```

### Security configuration
Besides common bundle configuration you will have to add some parameters in `security` section:
```yaml
security:
    providers:
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
                provider: chain_provider
```