# FullText Criterion

Added a new criterion for solr which allow the search on multiple fields

Allow per field query boost

Example
```php
$query = new Query();
...
$query->query = new MultipleFieldsFullText(
    'search text',
    [
        'boost' => [
            'meta_title__text_t' => '3',
            'meta_intro__text_t' => '2'
        ]
    ]
);
```

