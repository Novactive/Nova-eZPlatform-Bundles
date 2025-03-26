# Nova Ibexa (and eZ Platform) Bundles

[![Build Status](https://github.com/Novactive/Nova-eZPlatform-Bundles/workflows/CI/badge.svg?branch=master)](https://github.com/Novactive/Nova-eZPlatform-Bundles/actions)

This is the Mono Repo that manages all the Novactive Ibexa Bundles that have each an independant sub repository.


🎀 **DOCUMENTATION** 📖: https://novactive.github.io/Nova-eZPlatform-Bundles/master/index.html

## Installation instructions

```bash
git clone
ddev start
make installibexa IBEXA_VERSION="4.*"
ddev describe
```

This will install the last version of Ibexa and bundles on top of it.

## Contribution

This project comes with Coding Standards and Tests.
To help you contribute a Makefile is available to simplify the actions.

Please comply with `make codeclean` and `make tests` before to push, your PR won't be merged otherwise.

## Managed Repositories

| Bundles                                                                                                                                                                                                                                                                             | Compatibility                                                                     | Licence(s)                              |
|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|-----------------------------------------------------------------------------------|-----------------------------------------|
| [2FABundle](https://github.com/Novactive/NovaeZ2FABundle): Brings 2 Factor Authentication!                                                                                                                                                                                          | ![eZ-Platform-2.x-UNSURE] ![eZ-Platform-3.x-OK] ![Ibexa-3.3.x-OK]                 | ![MIT]                                  |
| [Accelerator](https://github.com/Novactive/NovaeZAccelerator): Performance optimizations. It brings Asynchronicity using Symfony Messenger.                                                                                                                                         | ![eZ-Platform-3.x-OK] ![Ibexa-3.3.x-OK]                                           | ![MIT]                                  |
| [AlgoliaSearchEngine](https://github.com/Novactive/NovaeZAlgoliaSearchEngine): Complete Integration with Algolia! Criterions, Facets, and more!                                                                                                                                     | ![eZ-Platform-3.x-OK] ![Ibexa-3.3.x-OK]                                           | ![Commercial] ![GPL] ![CreativeCommons] |
| [CloudinaryBundle](https://github.com/Novactive/NovaeZCloudinaryBundle): Images optimizations and manipulations by Cloudinary on top of eZ variatons. It brings the power of Cloudinary in your project.                                                                            | ![eZ-Platform-2.x-OK] ![eZ-Platform-3.x-OK] ![Ibexa-3.3.x-OK]                     | ![MIT]                                  |
| [EditHelpBundle](https://github.com/Novactive/NovaeZEditHelpBundle): Display rich and personalised content on the native eZ Content creation/edition forms!                                                                                                                         | ![eZ-Platform-2.x-OK] ![eZ-Platform-3.x-OK] ![Ibexa-3.3.x-OK]                     | ![MIT]                                  |
| [EnhancedImageAssetBundle](https://github.com/Novactive/NovaeZEnhancedImageAssetBundle): New image field with focuspoint support                                                                                                                                                    | ![eZ-Platform-2.x-OK] ![eZ-Platform-3.x-UNSURE] ![Ibexa-3.3.x-OK] ![Ibexa-4.x-OK] | ![MIT]                                  |
| [ExtraBundle](https://github.com/Novactive/NovaeZExtraBundle): It provides helpers (twig, controllers, children provider) and a great Wrapper class to simplify Content and Location management.                                                                                    | ![eZ-Platform-2.x-OK] ![eZ-Platform-3.x-OK] ![Ibexa-3.3.x-OK]                     | ![MIT]                                  |
| [FastlyImageOptimizerBundle](https://github.com/Novactive/NovaeZFastlyImageOptimizerBundle): Images optimizations and manipulations by Fastly Image Optimizer on top of eZ variatons.                                                                                               | ![eZ-Platform-3.x-OK] ![Ibexa-3.3.x-OK]                                           | ![MIT]                                  |
| [LdapAuthenticatorBundle](https://github.com/Novactive/NovaeZLdapAuthenticatorBundle): LDAP authenticator. It allows you to connect your project and your LDAP server.                                                                                                              | ![eZ-Platform-2.x-UNSURE] ![eZ-Platform-3.x-UNSURE] ![Ibexa-3.3.x-UNSURE]         | ![MIT]                                  |
| [MailingBundle](https://github.com/Novactive/NovaeZMailingBundle): Campaigns, Registrations, Mailings, Users etc. all you need. It provides a complete set of tools to manage, build, test and send your mailings and newsletters.                                                  | ![eZ-Platform-2.x-OK] ![eZ-Platform-3.x-OK] ![Ibexa-3.3.x-OK]                     | ![MIT]                                  |
| [MaintenanceBundle](https://github.com/Novactive/NovaeZMaintenanceBundle): Easily enable an maintenance page!                                                                                                                                                                       | ![eZ-Platform-3.x-OK] ![Ibexa-3.3.x-OK]                                           | ![MIT]                                  |
| [MenuManagerBundle](https://github.com/Novactive/NovaeZMenuManagerBundle): Module that allow the contributor to create and manage menu in the admin UI                                                                                                                              | ![eZ-Platform-2.x-OK] ![Ibexa-3.3.x-OK] ![Ibexa-4.x-OK]                           | ![MIT]                                  |
| [ProtectedContentBundle](https://github.com/Novactive/NovaeZProtectedContentBundle): Protect contents via a simple password without changing the Content Type. Simplest paywall-like mechanism to protect a content. It just works and no session used!                             | ![eZ-Platform-2.x-OK] ![eZ-Platform-3.x-OK] ![Ibexa-3.3.x-OK] ![Ibexa-4.x-OK]]    | ![MIT]                                  |
| [ResponsiveImagesBundle](https://github.com/Novactive/NovaeZResponsiveImagesBundle): Display your image with srcset in a Responsive way with doing anything beside creating the variation configuration                                                                             | ![eZ-Platform-2.x-OK] ![eZ-Platform-3.x-OK] ![Ibexa-3.3.x-OK]                     | ![MIT]                                  |
| [RssFeedBundle](https://github.com/Novactive/NovaeZRssFeedBundle): Get the RSS feed of the selected locations including selected content fields using specified url                                                                                                                 | ![eZ-Platform-2.x-OK] ![eZ-Platform-3.x-OK] ![Ibexa-3.3.x-OK] ![Ibexa-4.x-OK]     | ![MIT]                                  |
| [SEOBundle](https://github.com/Novactive/NovaeZSEOBundle): Optimized SEO management. Bundle that provides and simplifies all your SEO management, metas, sitemaps, robots.txt, etc.                                                                                                 | ![eZ-Platform-2.x-OK] ![eZ-Platform-3.x-OK] ![Ibexa-3.3.x-OK] ![Ibexa-4.x-OK]     | ![MIT]                                  |
| [SamlBundle](https://github.com/Novactive/AlmaviaCXIbexaSamlBundle): Bundle that add a way to connect to Ibexa using the SAML protocol   | ![Ibexa-4.x-OK]                      | ![MIT]                                  |
| [SlackBundle](https://github.com/Novactive/NovaeZSlackBundle): Control your DXP with this complete Slack integration. It allows a 2-way communication between your Slack workspace and your eZ Content Repository. Build custom publication workflow and use them from your mobile! | ![eZ-Platform-2.x-OK] ![eZ-Platform-3.x-NOK] ![Ibexa-3.3.x-NOK]                   | ![MIT]                                  |
| [SolrSearchExtraBundle](https://github.com/Novactive/NovaeZSolrSearchExtraBundle): Solr search handler additions. It adds many things, binary file plain text content indexation, fullText criterion, custom field configuration, exact matches boosting configuration, etc.        | ![eZ-Platform-2.x-OK] ![eZ-Platform-3.x-UNSURE] ![Ibexa-3.3.x-UNSURE]             | ![MIT]                                  |
| [StaticTemplatesBundle](https://github.com/Novactive/NovaeZStaticTemplatesBundle): Render twig templates via their paths through the design engine mechanism. Simple and perfect tiny bundle to build your Front-end first using Twig.                                              | ![eZ-Platform-2.x-OK] ![eZ-Platform-3.x-OK] ![Ibexa-3.3.x-OK] ![Ibexa-4.x-OK]     | ![MIT]                                  |
| [TranslationUiBundle](https://github.com/Novactive/AlmaviaCXIbexaTranslationUiBundle): This bundle allows to import translation files content into the database and provide a GUI to edit translations.                                                                             | ![Ibexa-4.x-OK]                                                                   | ![MIT]                                  |
| [SélectionFieldType](https://github.com/Novactive/Ibexa-FieldTypes): Enhanced Ibexa sélection type for Ibexa: possibility to add configure selection option by yaml.                                                                                                                | ![Ibexa-4.x-OK]                                                                   | ![MIT]                                  |

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


[Ibexa-4.x-OK]: https://img.shields.io/badge/Ibexa-4.x-green?style=flat-square&labelColor=black&logo=data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB3aWR0aD0iMTAyNCIgaGVpZ2h0PSIzODAiIHZpZXdCb3g9IjAgMCAxMDI0IDM4MCI+CiAgPGRlZnM+CiAgICA8bGluZWFyR3JhZGllbnQgaWQ9ImxpbmVhci1ncmFkaWVudCIgeTE9IjAuNSIgeDI9IjEiIHkyPSIwLjUiIGdyYWRpZW50VW5pdHM9Im9iamVjdEJvdW5kaW5nQm94Ij4KICAgICAgPHN0b3Agb2Zmc2V0PSIwIiBzdG9wLWNvbG9yPSIjZTMzZTEwIi8+CiAgICAgIDxzdG9wIG9mZnNldD0iMC4xNzkiIHN0b3AtY29sb3I9IiNlMTMwMWEiLz4KICAgICAgPHN0b3Agb2Zmc2V0PSIwLjUxNCIgc3RvcC1jb2xvcj0iI2RjMGMzNCIvPgogICAgICA8c3RvcCBvZmZzZXQ9IjAuNjE4IiBzdG9wLWNvbG9yPSIjZGIwMDNlIi8+CiAgICA8L2xpbmVhckdyYWRpZW50PgogICAgPGNsaXBQYXRoIGlkPSJjbGlwLWliZXhhX2NvbG9yZWRfYnJpZ2h0Ij4KICAgICAgPHJlY3Qgd2lkdGg9IjEwMjQiIGhlaWdodD0iMzgwIi8+CiAgICA8L2NsaXBQYXRoPgogIDwvZGVmcz4KICA8ZyBpZD0iaWJleGFfY29sb3JlZF9icmlnaHQiIGNsaXAtcGF0aD0idXJsKCNjbGlwLWliZXhhX2NvbG9yZWRfYnJpZ2h0KSI+CiAgICA8cGF0aCBpZD0iUGF0aF8xIiBkYXRhLW5hbWU9IlBhdGggMSIgZD0iTTg0Mi4wNTksMjMyLjY0NEg3ODIuNzhMNzAwLjQyMiwzMTUsNjgyLjE1LDI5Ny44bC0yOS42MzksMjkuNjQsMTguMjcxLDE3LjItNDUuMTQxLDQ1LjE0MS0uMDM1LS4wMjljLTExLjk3OCw5Ljg5NS0zNS4wNDcsMjMuNS01MS43NjcsMjMuNWE2OC44ODEsNjguODgxLDAsMCwxLTI4LjQ3Mi02LjE4MlM2NjAuMTUsMjkwLjY1Myw2NjcuNSwyODMuM2wtNi4yNTUtNi41NjFjLTI2LjQxMy0yNy4wODEtNTguMTE1LTQzLjE1LTg4LjI0OS00My4xNWExMTEuMTM5LDExMS4xMzksMCwwLDAsMCwyMjIuMjc3YzI4LjY2NCwwLDUzLjc4MS05LjU3LDg0Ljk4LTM5LjIwOGw0Mi40NC00Mi4zNzUsNzcuMTY0LDc3LjJoNTkuMjhsLTEwNi44LTEwNi44NDJaTTUwNC40NzUsMzQzLjg4NmE2OS40NDMsNjkuNDQzLDAsMCwxLDY5LjM2NC02OS4zNjQsNjguNzQ4LDY4Ljc0OCwwLDAsMSwzNy4zNDMsMTAuOTlsLTk1LjcwOSw5NS43MDlBNjguODgxLDY4Ljg4MSwwLDAsMSw1MDQuNDc1LDM0My44ODZaTTE0OS45MjQsMzMyLjAxNSwxODAuMSwzMDEuNlY0NTUuMjgxSDEzNy40OFYzNjIuMjI3QTQyLjksNDIuOSwwLDAsMSwxNDkuOTI0LDMzMi4wMTVaTTE4MC4xLDIyNy40MjZ2MjYuNmEuMTE1LjExNSwwLDAsMS0uMDM1LjA4M2wtMzIuMzIsMzIuMzJhNi4wMTMsNi4wMTMsMCwwLDEtMTAuMjY1LTQuMjUxVjIyNy40MjZhLjExNy4xMTcsMCwwLDEsLjExNi0uMTE2aDQyLjM4OEEuMTE3LjExNywwLDAsMSwxODAuMSwyMjcuNDI2Wm04NTcuMzgsMjAwLjYzOS0uMTczLTgzLjM0MXYtLjA1OWwwLTIuNC0uMDI0LjA1MmExMTEuMjQ1LDExMS4yNDUsMCwxLDAtNDIuNjg2LDg5LjlsLS4xODMsMzguNjE2Wk05MjUuMzIzLDQxNC45MDlhNjkuMzY1LDY5LjM2NSwwLDEsMSw2OS4zNjQtNjkuMzY0QTY5LjQ0NCw2OS40NDQsMCwwLDEsOTI1LjMyMyw0MTQuOTA5Wk0zMzAuOTQyLDIzM2ExMTAuNiwxMTAuNiwwLDAsMC02OC40MjMsMjMuNjE3bC4xODMtMzguNjE0TDIxOS42MywyNjAuNzc2bC4xNzUsODMuMzQyLDAsLjAyNCwwLC4wMzMsMCwyLjQuMDI0LS4wNTJBMTExLjEzNiwxMTEuMTM2LDAsMSwwLDMzMC45NDIsMjMzWm0uODQ0LDE3OS42NTdBNjkuMzY1LDY5LjM2NSwwLDEsMSw0MDEuMTUzLDM0My4zLDY5LjQ0Myw2OS40NDMsMCwwLDEsMzMxLjc4Nyw0MTIuNjYyWiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTc1LjQ4IC0xNTQuMDA3KSIgZmlsbD0idXJsKCNsaW5lYXItZ3JhZGllbnQpIi8+CiAgPC9nPgo8L3N2Zz4=

[Ibexa-3.3.x-OK]: https://img.shields.io/badge/Ibexa-3.3.x-green?style=flat-square&labelColor=black&logo=data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB3aWR0aD0iMTAyNCIgaGVpZ2h0PSIzODAiIHZpZXdCb3g9IjAgMCAxMDI0IDM4MCI+CiAgPGRlZnM+CiAgICA8bGluZWFyR3JhZGllbnQgaWQ9ImxpbmVhci1ncmFkaWVudCIgeTE9IjAuNSIgeDI9IjEiIHkyPSIwLjUiIGdyYWRpZW50VW5pdHM9Im9iamVjdEJvdW5kaW5nQm94Ij4KICAgICAgPHN0b3Agb2Zmc2V0PSIwIiBzdG9wLWNvbG9yPSIjZTMzZTEwIi8+CiAgICAgIDxzdG9wIG9mZnNldD0iMC4xNzkiIHN0b3AtY29sb3I9IiNlMTMwMWEiLz4KICAgICAgPHN0b3Agb2Zmc2V0PSIwLjUxNCIgc3RvcC1jb2xvcj0iI2RjMGMzNCIvPgogICAgICA8c3RvcCBvZmZzZXQ9IjAuNjE4IiBzdG9wLWNvbG9yPSIjZGIwMDNlIi8+CiAgICA8L2xpbmVhckdyYWRpZW50PgogICAgPGNsaXBQYXRoIGlkPSJjbGlwLWliZXhhX2NvbG9yZWRfYnJpZ2h0Ij4KICAgICAgPHJlY3Qgd2lkdGg9IjEwMjQiIGhlaWdodD0iMzgwIi8+CiAgICA8L2NsaXBQYXRoPgogIDwvZGVmcz4KICA8ZyBpZD0iaWJleGFfY29sb3JlZF9icmlnaHQiIGNsaXAtcGF0aD0idXJsKCNjbGlwLWliZXhhX2NvbG9yZWRfYnJpZ2h0KSI+CiAgICA8cGF0aCBpZD0iUGF0aF8xIiBkYXRhLW5hbWU9IlBhdGggMSIgZD0iTTg0Mi4wNTksMjMyLjY0NEg3ODIuNzhMNzAwLjQyMiwzMTUsNjgyLjE1LDI5Ny44bC0yOS42MzksMjkuNjQsMTguMjcxLDE3LjItNDUuMTQxLDQ1LjE0MS0uMDM1LS4wMjljLTExLjk3OCw5Ljg5NS0zNS4wNDcsMjMuNS01MS43NjcsMjMuNWE2OC44ODEsNjguODgxLDAsMCwxLTI4LjQ3Mi02LjE4MlM2NjAuMTUsMjkwLjY1Myw2NjcuNSwyODMuM2wtNi4yNTUtNi41NjFjLTI2LjQxMy0yNy4wODEtNTguMTE1LTQzLjE1LTg4LjI0OS00My4xNWExMTEuMTM5LDExMS4xMzksMCwwLDAsMCwyMjIuMjc3YzI4LjY2NCwwLDUzLjc4MS05LjU3LDg0Ljk4LTM5LjIwOGw0Mi40NC00Mi4zNzUsNzcuMTY0LDc3LjJoNTkuMjhsLTEwNi44LTEwNi44NDJaTTUwNC40NzUsMzQzLjg4NmE2OS40NDMsNjkuNDQzLDAsMCwxLDY5LjM2NC02OS4zNjQsNjguNzQ4LDY4Ljc0OCwwLDAsMSwzNy4zNDMsMTAuOTlsLTk1LjcwOSw5NS43MDlBNjguODgxLDY4Ljg4MSwwLDAsMSw1MDQuNDc1LDM0My44ODZaTTE0OS45MjQsMzMyLjAxNSwxODAuMSwzMDEuNlY0NTUuMjgxSDEzNy40OFYzNjIuMjI3QTQyLjksNDIuOSwwLDAsMSwxNDkuOTI0LDMzMi4wMTVaTTE4MC4xLDIyNy40MjZ2MjYuNmEuMTE1LjExNSwwLDAsMS0uMDM1LjA4M2wtMzIuMzIsMzIuMzJhNi4wMTMsNi4wMTMsMCwwLDEtMTAuMjY1LTQuMjUxVjIyNy40MjZhLjExNy4xMTcsMCwwLDEsLjExNi0uMTE2aDQyLjM4OEEuMTE3LjExNywwLDAsMSwxODAuMSwyMjcuNDI2Wm04NTcuMzgsMjAwLjYzOS0uMTczLTgzLjM0MXYtLjA1OWwwLTIuNC0uMDI0LjA1MmExMTEuMjQ1LDExMS4yNDUsMCwxLDAtNDIuNjg2LDg5LjlsLS4xODMsMzguNjE2Wk05MjUuMzIzLDQxNC45MDlhNjkuMzY1LDY5LjM2NSwwLDEsMSw2OS4zNjQtNjkuMzY0QTY5LjQ0NCw2OS40NDQsMCwwLDEsOTI1LjMyMyw0MTQuOTA5Wk0zMzAuOTQyLDIzM2ExMTAuNiwxMTAuNiwwLDAsMC02OC40MjMsMjMuNjE3bC4xODMtMzguNjE0TDIxOS42MywyNjAuNzc2bC4xNzUsODMuMzQyLDAsLjAyNCwwLC4wMzMsMCwyLjQuMDI0LS4wNTJBMTExLjEzNiwxMTEuMTM2LDAsMSwwLDMzMC45NDIsMjMzWm0uODQ0LDE3OS42NTdBNjkuMzY1LDY5LjM2NSwwLDEsMSw0MDEuMTUzLDM0My4zLDY5LjQ0Myw2OS40NDMsMCwwLDEsMzMxLjc4Nyw0MTIuNjYyWiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTc1LjQ4IC0xNTQuMDA3KSIgZmlsbD0idXJsKCNsaW5lYXItZ3JhZGllbnQpIi8+CiAgPC9nPgo8L3N2Zz4=

[eZ-Platform-3.x-OK]: https://img.shields.io/badge/eZ%20Platform-3.x-green?style=flat-square&labelColor=black&logo=data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DQo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkxheWVyXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4Ig0KCSB2aWV3Qm94PSIwIDAgMTY2LjcgMTY2LjciIGVuYWJsZS1iYWNrZ3JvdW5kPSJuZXcgMCAwIDE2Ni43IDE2Ni43IiB4bWw6c3BhY2U9InByZXNlcnZlIj4NCjxnPg0KCTxnPg0KCQk8Zz4NCgkJCTxnPg0KCQkJCTxyZWN0IHg9IjgiIHk9IjE1LjUiIHdpZHRoPSIxMjIuMyIgaGVpZ2h0PSIxMjIuMyIvPg0KCQkJCTxyZWN0IHg9IjE5LjkiIHk9IjI3LjQiIGZpbGw9IiNGRkZGRkYiIHdpZHRoPSI5OC42IiBoZWlnaHQ9Ijk4LjYiLz4NCgkJCQk8cmVjdCB4PSI1OC43IiB5PSI2NS44IiB3aWR0aD0iOTQuMSIgaGVpZ2h0PSI5NC4xIi8+DQoJCQkJPHJlY3QgeD0iNjkuOSIgeT0iNzciIGZpbGw9IiNGMTVBMjIiIHdpZHRoPSI3MS41IiBoZWlnaHQ9IjcxLjUiLz4NCgkJCTwvZz4NCgkJPC9nPg0KCTwvZz4NCjwvZz4NCjwvc3ZnPg0K
[eZ-Platform-2.x-OK]: https://img.shields.io/badge/eZ%20Platform-2.x-green?style=flat-square&labelColor=black&logo=data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DQo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkxheWVyXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4Ig0KCSB2aWV3Qm94PSIwIDAgMTY2LjcgMTY2LjciIGVuYWJsZS1iYWNrZ3JvdW5kPSJuZXcgMCAwIDE2Ni43IDE2Ni43IiB4bWw6c3BhY2U9InByZXNlcnZlIj4NCjxnPg0KCTxnPg0KCQk8Zz4NCgkJCTxnPg0KCQkJCTxyZWN0IHg9IjgiIHk9IjE1LjUiIHdpZHRoPSIxMjIuMyIgaGVpZ2h0PSIxMjIuMyIvPg0KCQkJCTxyZWN0IHg9IjE5LjkiIHk9IjI3LjQiIGZpbGw9IiNGRkZGRkYiIHdpZHRoPSI5OC42IiBoZWlnaHQ9Ijk4LjYiLz4NCgkJCQk8cmVjdCB4PSI1OC43IiB5PSI2NS44IiB3aWR0aD0iOTQuMSIgaGVpZ2h0PSI5NC4xIi8+DQoJCQkJPHJlY3QgeD0iNjkuOSIgeT0iNzciIGZpbGw9IiNGMTVBMjIiIHdpZHRoPSI3MS41IiBoZWlnaHQ9IjcxLjUiLz4NCgkJCTwvZz4NCgkJPC9nPg0KCTwvZz4NCjwvZz4NCjwvc3ZnPg0K

[eZ-Platform-3.x-UNSURE]: https://img.shields.io/badge/eZ%20Platform-3.x-orange?style=flat-square&labelColor=black&logo=data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DQo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkxheWVyXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4Ig0KCSB2aWV3Qm94PSIwIDAgMTY2LjcgMTY2LjciIGVuYWJsZS1iYWNrZ3JvdW5kPSJuZXcgMCAwIDE2Ni43IDE2Ni43IiB4bWw6c3BhY2U9InByZXNlcnZlIj4NCjxnPg0KCTxnPg0KCQk8Zz4NCgkJCTxnPg0KCQkJCTxyZWN0IHg9IjgiIHk9IjE1LjUiIHdpZHRoPSIxMjIuMyIgaGVpZ2h0PSIxMjIuMyIvPg0KCQkJCTxyZWN0IHg9IjE5LjkiIHk9IjI3LjQiIGZpbGw9IiNGRkZGRkYiIHdpZHRoPSI5OC42IiBoZWlnaHQ9Ijk4LjYiLz4NCgkJCQk8cmVjdCB4PSI1OC43IiB5PSI2NS44IiB3aWR0aD0iOTQuMSIgaGVpZ2h0PSI5NC4xIi8+DQoJCQkJPHJlY3QgeD0iNjkuOSIgeT0iNzciIGZpbGw9IiNGMTVBMjIiIHdpZHRoPSI3MS41IiBoZWlnaHQ9IjcxLjUiLz4NCgkJCTwvZz4NCgkJPC9nPg0KCTwvZz4NCjwvZz4NCjwvc3ZnPg0K
[eZ-Platform-2.x-UNSURE]: https://img.shields.io/badge/eZ%20Platform-2.x-orange?style=flat-square&labelColor=black&logo=data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DQo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkxheWVyXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4Ig0KCSB2aWV3Qm94PSIwIDAgMTY2LjcgMTY2LjciIGVuYWJsZS1iYWNrZ3JvdW5kPSJuZXcgMCAwIDE2Ni43IDE2Ni43IiB4bWw6c3BhY2U9InByZXNlcnZlIj4NCjxnPg0KCTxnPg0KCQk8Zz4NCgkJCTxnPg0KCQkJCTxyZWN0IHg9IjgiIHk9IjE1LjUiIHdpZHRoPSIxMjIuMyIgaGVpZ2h0PSIxMjIuMyIvPg0KCQkJCTxyZWN0IHg9IjE5LjkiIHk9IjI3LjQiIGZpbGw9IiNGRkZGRkYiIHdpZHRoPSI5OC42IiBoZWlnaHQ9Ijk4LjYiLz4NCgkJCQk8cmVjdCB4PSI1OC43IiB5PSI2NS44IiB3aWR0aD0iOTQuMSIgaGVpZ2h0PSI5NC4xIi8+DQoJCQkJPHJlY3QgeD0iNjkuOSIgeT0iNzciIGZpbGw9IiNGMTVBMjIiIHdpZHRoPSI3MS41IiBoZWlnaHQ9IjcxLjUiLz4NCgkJCTwvZz4NCgkJPC9nPg0KCTwvZz4NCjwvZz4NCjwvc3ZnPg0K
[Ibexa-3.3.x-UNSURE]: https://img.shields.io/badge/Ibexa-3.3.x-orange?style=flat-square&labelColor=black


[eZ-Platform-3.x-NOK]: https://img.shields.io/badge/eZ%20Platform-3.x-red?style=flat-square&labelColor=black&logo=data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DQo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkxheWVyXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4Ig0KCSB2aWV3Qm94PSIwIDAgMTY2LjcgMTY2LjciIGVuYWJsZS1iYWNrZ3JvdW5kPSJuZXcgMCAwIDE2Ni43IDE2Ni43IiB4bWw6c3BhY2U9InByZXNlcnZlIj4NCjxnPg0KCTxnPg0KCQk8Zz4NCgkJCTxnPg0KCQkJCTxyZWN0IHg9IjgiIHk9IjE1LjUiIHdpZHRoPSIxMjIuMyIgaGVpZ2h0PSIxMjIuMyIvPg0KCQkJCTxyZWN0IHg9IjE5LjkiIHk9IjI3LjQiIGZpbGw9IiNGRkZGRkYiIHdpZHRoPSI5OC42IiBoZWlnaHQ9Ijk4LjYiLz4NCgkJCQk8cmVjdCB4PSI1OC43IiB5PSI2NS44IiB3aWR0aD0iOTQuMSIgaGVpZ2h0PSI5NC4xIi8+DQoJCQkJPHJlY3QgeD0iNjkuOSIgeT0iNzciIGZpbGw9IiNGMTVBMjIiIHdpZHRoPSI3MS41IiBoZWlnaHQ9IjcxLjUiLz4NCgkJCTwvZz4NCgkJPC9nPg0KCTwvZz4NCjwvZz4NCjwvc3ZnPg0K
[Ibexa-3.3.x-NOK]: https://img.shields.io/badge/Ibexa-3.3.x-red?style=flat-square&labelColor=black

[MIT]: https://img.shields.io/badge/license-MIT-green?style=flat-square&labelColor=black
[Commercial]: https://img.shields.io/badge/license-Commercial-red?style=flat-square&labelColor=black
[GPL]: https://img.shields.io/badge/license-GPL-blue?style=flat-square&labelColor=black
[CreativeCommons]: https://img.shields.io/badge/license-CreativeCommons-yellow?style=flat-square&labelColor=black















