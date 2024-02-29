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
```

The following parameters are also available to tweak the behavior
```yaml
# Content Id or Remote Content Id of the user group where new users will be created
almaviacx.saml.config.default.user_group_id: '%env(resolve:SAML_SERVICE_PROVIDER_USER_GROUP_ID)%'

# Map user content type fields to the saml response attributes
almaviacx.saml.config.default.user_attributes_mapping:
    first_name: http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name
    last_name: http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name
```

## 
