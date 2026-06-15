# Novactive eZ Menu Manager Bundle

## Upgrade Instructions

### Upgrading to the self-contained front-end version

`jstree` is now shipped with the bundle and `reactstrap` is no longer used: the
edit forms rely on the Bootstrap markup already provided by the Ibexa back
office.

If you previously added `jstree` and/or `reactstrap` to your project as a
workaround for the webpack build failure, remove them and rebuild your assets:

```bash
yarn remove jstree reactstrap
php bin/console ibexa:encore:compile
```

Depending on your project setup, the back-office assets may be compiled by a
dedicated script instead (e.g. `yarn ibexa:dev`).
