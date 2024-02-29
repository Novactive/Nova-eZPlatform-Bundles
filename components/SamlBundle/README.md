# AlmaviaCX Ibexa SAML Bundle


routing
```yaml

hslavich_saml_sp:
  resource: "@HslavichOneloginSamlBundle/Resources/config/routing.yml"

```

security
```yaml


            saml:
                use_attribute_friendly_name: true
                check_path: saml_acs
                login_path: saml_login
                failure_path: saml_login
                default_target_path: /dashboard
                always_use_default_target_path: true
                user_factory: almaviacx.saml.user_factory
```


env
```
SAML_IDENTITY_PROVIDER_ENTITYID=
SAML_IDENTITY_PROVIDER_LOGIN_URL=
SAML_IDENTITY_PROVIDER_LOGIN_BINDING=
SAML_IDENTITY_PROVIDER_LOGOUT_URL=
SAML_IDENTITY_PROVIDER_LOGOUT_BINDING=
SAML_IDENTITY_PROVIDER_X509_CERT=
SAML_SERVICE_PROVIDER_URL=
SAML_SERVICE_PROVIDER_NAME_ID_FORMAT=
```
