# Novactive eZ Mailing Bundle

## Features

[Features](bundle/Resources/doc/FEATURES.md)

## Usage and installation instructions

[Usage](bundle/Resources/doc/USAGE.md)

[Installation](bundle/Resources/doc/INSTALL.md)


## Contributions (process to be tested)
----------------

To install the development environment.

Get a docker container with MariaDB

```bash
docker run -d -p 3337:3306 --name dbnovaezmailingcontainer -e MYSQL_ROOT_PASSWORD=ezplatform mariadb:10.2
```

Git clone

```bash
git clone git@github.com:Novactive/NovaeZMailingBundle.git
cd NovaeZMailingBundle
```

And then run

```bash
composer create-project ezsystems/ezplatform --prefer-dist --no-progress --no-interaction --no-scripts
curl -o tests/platform.sh/wrap.php https://raw.githubusercontent.com/Plopix/symfony-bundle-app-wrapper/master/wrap-bundle.php
WRAP_APP_DIR=./ezplatform WRAP_BUNDLE_DIR=./ php tests/platform.sh/wrap.php
cd ezplatform
composer update --lock
bin/console doctrine:database:create
bin/console ezplatform:install clean
bin/console novaezmailing:install
bin/console doctrine:fixtures:load --no-interaction
bin/console cache:clear
```


Change and License
------------------

[Changelog](bundle/Resources/doc/CHANGELOG.md)

[License](LICENSE)


Special Mentions and Credits
----------------------------
