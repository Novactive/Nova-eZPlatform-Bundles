# Novactive eZ 2FA Bundle


## Upgrade Instructions


Refer to the sql files inside the `Resources/sql` folder and run the particular queries inside the file depending on the version you're upgrading the Bundle to.

#### Upgrade from 1.2.0 to 1.3.0

- Run the query from the `Resources/sql/upgrade-1.2.0-to.1.3.0.sql` file.


#### Upgrade from 1.3.0 to 1.4.0

- Run the query from the `Resources/sql/upgrade-1.3.0-to.1.4.0.sql` file.
- Rename the **2fa_method** to **2fa_mobile_method** inside the bundle config file (`nova_ez2fa.yaml`).