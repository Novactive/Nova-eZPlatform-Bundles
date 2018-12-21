# Usage

This **NovaEzMailing** Bundle is the upgraded version of **CJW Newsletter** and **Ez Mailing** bundles.
After the bundle is installed within _Ez Platform_ which already contains one of those old bundles the database can be migrated
to the new Bundle.

1. If the old bundle is **CJW Newletter** run the following commands inside _ezplatform_ folder:

    - php bin/console novaezmailing:migrate:cjwnl --export
    - php bin/console novaezmailing:migrate:cjwnl --import
    
2. If the old bundle is **Ez Mailing** run the following commands inside _ezplatform_ folder:
    - php bin/console novaezmailing:migrate:ezmailing --export
    - php bin/console novaezmailing:migrate:ezmailing --import

The first one exports the data from the old database to json files.
The second one imports the data from json files to the new database.
After that the dumped data is still in the json files inside web/var/site/files/migrate/cjwnl folder 
split between folders _campaign_, _list_, _user_. They can be removed manually if they are not needed anymore.

There is also the option for both cases to truncate the current **NovaEzMailing** **Bundle** tables in the database:
- php bin/console novaezmailing:migrate:cjwnl --clean

or
- php bin/console novaezmailing:migrate:ezmailing --clean 