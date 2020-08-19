# Nova eZ Platform Bundles

This is the Mono Repo that manages all the Novactive eZ Bundle that have each an independant sub repository.

## Installation instructions

```bash
git clone
make installez
```

This will install eZ Platform and bundles on top of it.

## Contribution

This project comes with Coding Standards and Tests.
To help you contribute a Makefile is available to simplify the actions.

Please comply with `make codeclean` and `make tests` before to push, your PR won't be merged otherwise.

## Managed Repositories

- https://github.com/Novactive/NovaeZAccelerator
- https://github.com/Novactive/NovaeZSEOBundle
- https://github.com/Novactive/NovaeZMailingBundle
- https://github.com/Novactive/NovaeZProtectedContentBundle
- https://github.com/Novactive/NovaeZSlackBundle
- https://github.com/Novactive/NovaeZExtraBundle
- https://github.com/Novactive/NovaeZLdapAuthenticatorBundle
- https://github.com/Novactive/NovaeZStaticTemplatesBundle
- https://github.com/Novactive/NovaeZSolrSearchExtraBundle
- https://github.com/Novactive/NovaeZCloudinaryBundle
- https://github.com/Novactive/NovaeZResponsiveImagesBundle
- https://github.com/Novactive/NovaeZMenuManagerBundle
- https://github.com/Novactive/NovaeZEnhancedImageAssetBundle
- https://github.com/Novactive/NovaeZEditHelpBundle
- https://github.com/Novactive/NovaeZRssFeedBundle


## For Maintainers

> "With great power comes great responsabilities" - Spiderman's uncle Ben.

### Synchronize Mono to Many

This will spit/synchronize the branch you will provide accross all the sub repository when needed.

```bash
bin/releaser sync
```

> Then follow the wizard.


### Tag a new version of a specific repo

This will tag the branch you will provide on the sub repository

```bash
bin/releaser tag
```

> Then follow the wizard. 

### Adding a new Components

- create the component in the folder `components`
- create the Github sub-repository that MUST match NovaeZ${COMPONENT_NAME}
- add the autoload lines in the `./composer.json` INCLUDING `tests`
- setup the `ci-config.yaml` file to enable auto install and/or auto tests
- Packagist MUST still be configured in the sub-repository on Github
