# Reader

The job of a reader is to fetch the datas from somewhere and transmit them to the workflow processors.

A reader service must implement `AlmaviaCX\Bundle\IbexaImportExport\Reader\ReaderInterface` and have the tag `almaviacx.import_export.component` and provide an alias

```yaml
tags:
    - { name: almaviacx.import_export.component, alias: reader.csv}
```

The bundle provide the `AlmaviaCX\Bundle\IbexaImportExport\Reader\AbstractReader` to simplify the creation of a service

Using this abstraction, you just need to implement the `getName` and `__invoke` methods.

```injectablephp
public static function getName()
{
    return new TranslatableMessage('reader.csv.name', [], 'import_export');
}

public function __invoke(): Iterator
{
    // TODO: Implement __invoke() method.
}
```

You can also override the following functions :
- `getOptionsFormType` to provide the form type used to manage the reader options
- `getOptionsType` to provide a different options class


## Provided readers

### AlmaviaCX\Bundle\IbexaImportExport\Reader\Csv\CsvReader

Fetch rows from a CSV file.

Related options : `AlmaviaCX\Bundle\IbexaImportExport\Reader\Csv\CsvReader\CsvReaderOptions`

### AlmaviaCX\Bundle\IbexaImportExport\Reader\Ibexa\ContentList\IbexaContentListReader

Fetch a list of Ibexa contents

Related options : `AlmaviaCX\Bundle\IbexaImportExport\Reader\Ibexa\ContentList\IbexaContentListReaderOptions`
