# AlmaviaCX Ibexa OIDC Bundle

## Configuration

### Configure OIDC client

The following variables are used to define the different parameters for the OIDC endpoint communication
```
OIDC_ACCESS_TOKEN_METHOD='POST'
OIDC_ACCESS_TOKEN_RESOURCE_OWNER_ID=~
OIDC_SCOPE_SEPARATOR=','
OIDC_RESPONSE_ERROR='error'
OIDC_RESPONSE_CODE=~
OIDC_RESPONSE_RESOURCE_OWNER_ID='id'
OIDC_SCOPES=~
OIDC_PKCE_METHOD=~
```

To change the configuration based on siteaccess, it's possible to defined it under the folowing siteaccess aware parameter : `almaviacx.oidc.<siteaccess|siteaccess_group>`

The following parameters are also available to tweak the behavior
```yaml
    # Content Id or Remote Content Id of the user group where new users will be created
    almaviacx.oidc.config.default.user_group_id: '%env(resolve:OIDC_USER_GROUP_ID)%'

    # Method used to load existing users
    almaviacx.oidc.config.default.user_load_method: !php/const AlmaviaCX\Bundle\IbexaOidc\ResourceOwnerMapper\OidcResourceOwnerMapper::LOAD_METHOD_LOGIN

    # Map user content type fields to the oidc response attributes
    almaviacx.oidc.config.default.user_attributes_mapping: ~

    almaviacx.oidc.config.default.user_content_type_identifier: ~
```

### Enable OIDC client

The client needs to be a part of the [SiteAccess scope](multisite_configuration.md#scope).

``` yaml
ibexa:
    system:
        admin:
            oauth2:
                enabled: true
                clients: ['oidc']
```

### Configure firewall

In `config/packages/security.yaml`, enable the `ibexa_oauth2_connect` firewall and replace the `ibexa_front` firewall with the `ibexa_oauth2_front` one.

``` yaml
security:
    #…

    firewalls:
        #…

        # Uncomment ibexa_oauth2_connect, ibexa_oauth2_front rules and comment ibexa_front firewall
        # to enable OAuth2 authentication

        ibexa_oauth2_connect:
            pattern: /oauth2/connect/*
            security: false

        ibexa_oauth2_front:
            pattern: ^/
            user_checker: Ibexa\Core\MVC\Symfony\Security\UserChecker
            anonymous: ~
            ibexa_rest_session: ~
            guard:
                authenticators:
                    - Ibexa\Bundle\OAuth2Client\Security\Authenticator\OAuth2Authenticator
                    - Ibexa\PageBuilder\Security\EditorialMode\TokenAuthenticator
                entry_point: Ibexa\Bundle\OAuth2Client\Security\Authenticator\OAuth2Authenticator
            form_login:
                require_previous_session: false
                csrf_token_generator: security.csrf.token_manager
            logout: ~

        #ibexa_front:
        #    pattern: ^/
        #    user_checker: Ibexa\Core\MVC\Symfony\Security\UserChecker
        #    …
```

The `guard.authenticators` setting specifies the [Guard authenticators]([[= symfony_doc =]]/security/guard_authentication.html) to be used.

By adding the `Ibexa\Bundle\OAuth2Client\Security\Authenticator\OAuth2Authenticator` guard authenticator you add a possibility to use OAuth2 on those routes.

