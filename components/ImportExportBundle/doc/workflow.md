# Workflow

Workflow can be created throught the admin UI or as a Symfony service.

## Workflow service

The workflow service must implement `AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowInterface` and have the tag `almaviacx.import_export.workflow`
```yaml
App\ImportExport\Workflow\ImportContentWorkflow:
    tags:
        - { name: almaviacx.import_export.workflow }
```

The bundle provide the `AlmaviaCX\Bundle\IbexaImportExport\Workflow\AbstractWorkflow` to simplify the creation of a service.

Using this abstraction, you just need to implement the `getDefaultConfig` method in order to provide the configuration.

Exemple :
```injectablephp
public static function getDefaultConfig(): WorkflowConfiguration
{
    $configuration = new WorkflowConfiguration(
        'app.import_export.workflow.import_content',
        'Import content',
    );
    $readerOptions = new CsvReaderOptions();
    $readerOptions->headerRowNumber = 0;
    $configuration->setReader(
        CsvReader::class,
        $readerOptions
    );

    $writerOptions = new IbexaContentWriterOptions();
    $writerOptions->map = new ItemTransformationMap(
        [
            'contentRemoteId' => [
                'transformer' => SlugTransformer::class,
                'source' => new PropertyPath( '[name]' )
            ],
            'mainLanguageCode' => 'eng-GB',
            'contentTypeIdentifier' => 'article',
            'fields[eng-GB][title]' => new PropertyPath( '[name]' ),
            'fields[eng-GB][intro]' => new PropertyPath( '[intro]' ),
        ]
    );
    $configuration->addProcessor(
        IbexaContentWriter::class,
        $writerOptions
    );
    return $configuration;
}
```

Every options that are not specified this way will be asked when creating the job triggering this workflow
