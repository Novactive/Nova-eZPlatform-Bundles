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

What the migration script does:

1. When migrating from **CJW Newsletter**: 

    - It takes the records from the _list_ table to create the mailing lists and campaigns of them. 
    - As each record is related to some _Ez Content_ it selects the record with the latest version for each content due to _contentobject_attribute_version_ field. 
    - Then we take the mailings from _edition_send_ table for each campaign fetched from the _list_ table before.
    - After that the users with subscriptions are saved but only those subscriptions which have the _list_contentobject_id_ value that exists among _list_ records.

2. When migrating from **Ez Mailing**: 

    - It takes the the records from the old tables with mailing lists, campaigns and users, 
    but only those which don't have status _draft_.  
    - We take and save only those subscriptions for the users which are related to existing mailing lists.
    - We don't migrate amy mailings here because there is not enough data for that.


There is also the option for both cases to truncate the current **NovaEzMailing** **Bundle** tables in the database:
- php bin/console novaezmailing:migrate:cjwnl --clean

or
- php bin/console novaezmailing:migrate:ezmailing --clean 