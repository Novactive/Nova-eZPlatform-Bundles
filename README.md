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

Change the parameters:

```yaml
# app/config/parameters.yml
    env(DATABASE_DRIVER): pdo_mysql
    env(DATABASE_HOST): 127.0.0.1
    env(DATABASE_PORT): 3337
    env(DATABASE_NAME): ezplatform
    env(DATABASE_USER): root
    env(DATABASE_PASSWORD): ezplatform
```

And then run

```bash
composer create-project ezsystems/ezplatform --prefer-dist --no-progress --no-interaction --no-scripts
curl -o tests/platform.sh/wrap.php https://raw.githubusercontent.com/Plopix/symfony-bundle-app-wrapper/master/wrap-bundle.php
WRAP_APP_DIR=./ezplatform php tests/platform.sh/wrap.php
cd ezplatform
composer update --lock
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
