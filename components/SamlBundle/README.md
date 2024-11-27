# AlmaviaCX Ibexa SAML Bundle

## Configuration
Add the following config to the routing config file
```yaml

hslavich_saml_sp:
  resource: "@HslavichOneloginSamlBundle/Resources/config/routing.yml"

```

Add the following parameters to the security config file
```yaml

ibexa_saml_front:
    pattern: /saml/(login|metadata|logout)
    security: false

ibexa_front:
    ...
    saml:
        use_attribute_friendly_name: false
        check_path: saml_acs
        login_path: saml_login
        failure_path: saml_login
        default_target_path: /dashboard
        always_use_default_target_path: true
        user_factory: almaviacx.saml.user_factory
        # username_attribute:
```

The following variables are used to define the different parameters for the SAML endpoint communication
```
SAML_IDENTITY_PROVIDER_ENTITYID="..."
SAML_IDENTITY_PROVIDER_LOGIN_URL="..."
SAML_IDENTITY_PROVIDER_LOGIN_BINDING=urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect
SAML_IDENTITY_PROVIDER_LOGOUT_URL="..."
SAML_IDENTITY_PROVIDER_LOGOUT_BINDING=urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect
SAML_IDENTITY_PROVIDER_EMAIL_ATTRIBUTE="http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress"
SAML_IDENTITY_PROVIDER_X509_CERT="..."

SAML_SERVICE_PROVIDER_URL=https://novabundles.ddev.site/admin
SAML_SERVICE_PROVIDER_NAMEID_FORMAT='urn:oasis:names:tc:SAML:2.0:nameid-format:nameidentifier'
SAML_SERVICE_PROVIDER_USER_GROUP_ID=sdf1sd61868sd1fdsvc

env(SAML_IDENTITY_PROVIDER_EMAIL_ATTRIBUTE): ~
env(SAML_IDENTITY_PROVIDER_LOGIN_ATTRIBUTE): ~
```

These variables are used to define the following global configuration :
```
idp:
    entityId: '%env(resolve:SAML_IDENTITY_PROVIDER_ENTITYID)%'
    singleSignOnService:
        url: '%env(resolve:SAML_IDENTITY_PROVIDER_LOGIN_URL)%'
        binding: '%env(resolve:SAML_IDENTITY_PROVIDER_LOGIN_BINDING)%'
    singleLogoutService:
        url:  '%env(resolve:SAML_IDENTITY_PROVIDER_LOGOUT_URL)%'
        binding: '%env(resolve:SAML_IDENTITY_PROVIDER_LOGOUT_BINDING)%'
    x509cert: '%env(resolve:SAML_IDENTITY_PROVIDER_X509_CERT)%'
sp:
    entityId: '%env(resolve:SAML_SERVICE_PROVIDER_URL)%/saml/metadata'
    assertionConsumerService:
        url: '%env(resolve:SAML_SERVICE_PROVIDER_URL)%/saml/acs'
        binding: 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST'
    singleLogoutService:
        url: '%env(resolve:SAML_SERVICE_PROVIDER_URL)%/saml/logout'
        binding: 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect'
    NameIDFormat: '%env(resolve:SAML_SERVICE_PROVIDER_NAMEID_FORMAT)%'
baseurl: '%env(resolve:SAML_SERVICE_PROVIDER_URL)%/saml'
debug: '%kernel.debug%'
```

To change the configuration based on siteaccess, it's possible to defined it under the folowing siteaccess aware parameter : `almaviacx.saml.<siteaccess|siteaccess_group>.auth_settings`

The following parameters are also available to tweak the behavior
```yaml
# Attribute used to get the email address from
almaviacx.saml.identity.provider.email.attribute: '%env(resolve:SAML_IDENTITY_PROVIDER_EMAIL_ATTRIBUTE)%'
# Attribute used to get the login from (null = nameId or what is defined for the "username_attribute" parameter)
almaviacx.saml.identity.provider.login.attribute: '%env(resolve:SAML_IDENTITY_PROVIDER_LOGIN_ATTRIBUTE)%'

# Method used to load existing users 
almaviacx.saml.config.default.user_load_method: !php/const AlmaviaCX\Bundle\IbexaSaml\Security\Saml\SamlUserProvider::LOAD_METHOD_LOGIN

# Content Id or Remote Content Id of the user group where new users will be created
almaviacx.saml.config.default.user_group_id: '%env(resolve:SAML_SERVICE_PROVIDER_USER_GROUP_ID)%'

# Map user content type fields to the saml response attributes
almaviacx.saml.config.default.user_attributes_mapping:
    first_name: http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name
    last_name: http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name
```

## Load user by email instead of login

Configure the following parameters :
```yaml
# In security.yaml, configure the value for the following parameter :
username_attribute: '%almaviacx.saml.identity.provider.email.attribute%'
    
# As you don't want the email address to be used as login (Ibexa doesn't support special char in login), you need to configure this parameter :
almaviacx.saml.identity.provider.login.attribute: 
    
# Change the user load method
almaviacx.saml.config.default.user_load_method: !php/const AlmaviaCX\Bundle\IbexaSaml\Security\Saml\SamlUserProvider::LOAD_METHOD_EMAIL
```
