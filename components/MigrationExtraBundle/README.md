# AlmaviaCX Ibexa Migration Extra Bundle

## Features

Bundle that add extra steps and actions to the migration process of Ibexa DXP

### Move Step

New step used to move location 

```yaml
-
    type: location
    mode: move
    locationMatch:
        field: location_remote_id
        value: 514edde3eba45a14e1ea167f4d123708
    newParentLocationMatch:
        field: location_id
        value: 2
```

### Taxonomy ID reference

```yaml
-
    type: content
    mode: create
    ...
    references:
        -   name: taxonomy_activity_domain_root_taxonomy_id
            type: taxonomy_id
```
