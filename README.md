# Novactive eZ Solr Search Extra Bundle

[![Build Status](https://img.shields.io/travis/Novactive/NovaeZSolrSearchExtraBundle.svg?style=flat-square&branch=develop-ezplatform)](https://travis-ci.org/Novactive/NovaeZSolrSearchExtraBundle)
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
- **[Implemented]** [Exact matches boosting configuration](./doc/custom_meta_fields.md)
- **[WIP]** Boost doc based on publish date (newer docs score higher)
- **[WIP]** Highlighting 
- **[Implemented]** Manage stopwords and synonyms from eZ Platform admin interface

## Requirements

- eZ Platform
- Solr Search Engine Bundle for eZ Platform
- PHP 7.1+

## Installation

### Use Composer

Add NovaeZSolrSearchExtraBundle in your composer.json:

```bash
composer require novactive/ezsolrsearchextrabundle
```

### Register the bundle

Register the bundle in your application's kernel class:

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

## Docs
### Publish date boosting
http://lucene.apache.org/solr/guide/6_6/the-dismax-query-parser.html#TheDisMaxQueryParser-Thebf_BoostFunctions_Parameter

https://wiki.apache.org/solr/SolrRelevancyFAQ

### Highlighting
https://lucene.apache.org/solr/guide/6_6/highlighting.html

### Stopwords/Synonyms managment
https://lucene.apache.org/solr/guide/6_6/managed-resources.html
