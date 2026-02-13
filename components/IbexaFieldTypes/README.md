## AlmaviaCX Ibexa FieldTypes
This bundle add some field types to ibexa

## Installation
```bash
composer require almaviacx/ibexa-field-types
```

update `config/bundles.php` file by adding this:

```php
    AlmaviaCX\Ibexa\Bundle\FieldTypes\AlmaviaCXFieldTypesBundle::class => ['all' => true],,
```

## Configuration

### ACX selection fieldType

#### selection choices configuration
create a configuration `config/packages/acx_field_types.yaml` with the following
```yaml
acx_field_types:
  system:
     default: # or global or siteaccess GROUP
      acx_selection:
        my_selection_choices: # value of choices_entry in fiedltypes settings
          # Label: value
          "atlicon-grid": atlicon-grid
          atlicon-industry: atlicon-industry
          atlicon-people-simple: atlicon-people-simple
          atlicon-gears: atlicon-gears
```
#### template to render `my_selection_choices` field value
create a template accessible throught: `@ibexadesign/fields/acxselection/my_custom_choices.html.twig`
#### edit (or create) the content_type
 - add a field of type almaviacx selection
 - fieldSettings:
   - Choices entry: `my_selection_choices`
   - template: `my_custom_choices`
   - check single / multiple selection

update the template to your needs:
example for atlicon to diplay icon
`@ibexadesign/fields/acxselection/my_custom_choices.html.twig` content can be:
```twig
{% set selected_value = field.value.getSelection() %}
{% if selected_value is not empty %}
    <span class='atlicon {{ selected_value }}" aria-hidden="true"/>
{% endif %}
```
