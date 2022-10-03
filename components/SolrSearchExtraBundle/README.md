# Novactive eZ Solr Search Extra Bundle

----

This repository is what we call a "subtree split": a read-only copy of one directory of the main repository. 
It is used by Composer to allow developers to depend on specific bundles.

If you want to report or contribute, you should instead open your issue on the main repository: https://github.com/Novactive/Nova-eZPlatform-Bundles

Documentation is available in this repository via `.md` files but also packaged here: https://novactive.github.io/Nova-eZPlatform-Bundles/master/SolrSearchExtraBundle/README.md.html

----

[![Downloads](https://img.shields.io/packagist/dt/novactive/ezsolrsearchextrabundle.svg?style=flat-square)](https://packagist.org/packages/novactive/ezsolrsearchextrabundle)
[![Latest version](https://img.shields.io/github/release/Novactive/NovaeZSolrSearchExtraBundle.svg?style=flat-square)](https://github.com/Novactive/NovaeZSolrSearchExtraBundle/releases)
[![License](https://img.shields.io/packagist/l/novactive/ezsolrsearchextrabundle.svg?style=flat-square)](LICENSE)

An eZPlatform bundle which extend the solr search handler.
 
## Features

- **[Implemented]** [Binary file plain text content indexation in the full text field](./doc/file_indexation.md)
- **[Implemented]** [FullText criterion to with extra parameters](./doc/fulltext_criterion.md)
    - search and boost multiple fields
    - boost depending on publish date
    - boost on phrases matches
    - boost on exact matches
- **[Implemented]** [Custom field configuration](./doc/custom_fields.md)
- **[Implemented]** [Exact matches boosting configuration](./doc/exact_match_boost.md)
- **[WIP]** Boost doc based on publish date (newer docs score higher)
- **[WIP]** Highlighting 
- **[Implemented]** Manage stopwords and synonyms from eZ Platform admin interface

## Installation

### Use Composer

Add NovaeZSolrSearchExtraBundle in your composer.json:

```bash
composer require novactive/ezsolrsearchextrabundle
```

### Register the bundle

Then inject the bundle in the `bundles.php` of your application.

```php
    Novactive\EzSolrSearchExtraBundle\EzSolrSearchExtraBundle::class => [ 'all'=> true ],
```

```php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = array(
        // ...
        new Novactive\EzSolrSearchExtraBundle\EzSolrSearchExtraBundle(),
        // ...
    );
}
```

### Routing config

Add the following routing config

```yaml
solr:
    resource: "@EzSolrSearchExtraBundle/Controller/"
    type:     annotation
    prefix:   /
```

### Multiple date field

Add the following field to your solr schema

```xml
<dynamicField name="*_mdt" type="date" indexed="true" stored="true" multiValued="true"/>
```

## Docs
### Publish date boosting
http://lucene.apache.org/solr/guide/6_6/the-dismax-query-parser.html#TheDisMaxQueryParser-Thebf_BoostFunctions_Parameter

https://wiki.apache.org/solr/SolrRelevancyFAQ

### Highlighting
https://lucene.apache.org/solr/guide/6_6/highlighting.html

### Stopwords/Synonyms managment
https://lucene.apache.org/solr/guide/6_6/managed-resources.html
