# AlmaviaCX Ibexa Import/Export Bundle

Import / Export workflow :


A `job` trigger a `workflow`

A `workflow` call a `reader` to get a list of items, then use a list of `step` to filter/modify the items and finally pass them to a list of `writer`


## Step

A step service must implement `AlmaviaCX\Bundle\IbexaImportExport\Step\StepInterface` and have the tag `almaviacx.import_export.component`

The bundle provide the `AlmaviaCX\Bundle\IbexaImportExport\Step\AbstractStep` to simplify the creation of a service

### Provided steps

#### AlmaviaCX\Bundle\IbexaImportExport\Step\IbexaContentToArrayStep

Transform a content into an associative array. Take a map as argument to extract properties from a content to generate the associative array

More explaination on the transformation process [here](./doc/ibexa_content_to_array_step.md)

Options are :
- map (array representing the resulting associative array. each entry value correspond to a property of the content. ex : `["title" => "content.fields[title].value"]`)

## Writer

