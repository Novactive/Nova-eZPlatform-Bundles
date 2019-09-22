# FullText Criterion

Added a new criterion for solr with the following features :
- search and boost multiple fields
- boost depending on publish date ([Publish date field](./custom_fields.md#publish-date-field))
- boost on phrases matches
- boost on exact matches ([Exact matches boosting](./exact_match_boost.md))

Allow per field query boost

Example
```php
$query = new Query();
...
$query->query = new Novactive\EzSolrSearchExtra\Query\Content\Criterion\MultipleFieldsFullText(
    'search text',
    [
        'metaBoost' => [
            'title' => '3',
            'intro' => '2'
        ],
        'boostPublishDate' => true
    ]
);
```

