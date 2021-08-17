# Novactive eZ Algolia Search Engine Bundle

----

This repository is what we call a "subtree split": a read-only copy of one directory of the main repository. 
It is used by Composer to allow developers to depend on specific bundles.

If you want to report or contribute, you should instead open your issue on the main repository: https://github.com/Novactive/Nova-eZPlatform-Bundles

Documentation is available in this repository via `.md` files but also packaged here: https://novactive.github.io/Nova-eZPlatform-Bundles/master/AlgoliaSearchEngine/README.md.html

----

Novactive eZ Algolia Search Engine is an eZ Platform bundle to provide Algolia search integration, enabling you to use the Algolia Search Engine to index the data and to search.

Thanks to these 3 main features:

- **eZ Repository Search Service Handler implementation**: All basic use cases that are used by eZ Platform to retrieve the contents and locations 
are managed by this bundle behind the scene thanks to the Repository Search Service. 
This bunble implements all the visitors that enable the conversion from eZ Query instance (including Criterions, Sort Clauses, Facet Builders) into the request that is sent to Algolia.
- **Algolia PHP Client integration**: Content items are automatically indexed and you can execute Algolia queries directly bypassing the SearchService if you need to. Option is possible to hydrate results into `Content` or `Location` if needed.
- **Algolia Front End**: One of the many Algolia's benefits is the Frontend Components they provide to execute/implement the search using Front End technologies. Simplifying development and providing insane performances.
This bundle provides an example of that integration with React JS, with no style, and with Twitter Bootstrap. 


## Installation

### Requirements

* eZ Platform 3.1+
* PHP 7.3

### Installation steps

Add the following to your composer.json and run `composer update novactive/ezalgoliasearchengine` to install dependencies:

```json
# composer.json

"require": {
    "novactive/ezalgoliasearchengine": "^1.0.0"
}
```

### Javascript dependencies

```bash
cd ezplatform
yarn add --dev algoliasearch react react-collapsible react-dom react-instantsearch-dom
```

### Register the bundle

If Symfony Flex did not do it already, activate the bundle in `config\bundles.php` file.

```php
// config\bundles.php

return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    ...
    Novactive\Bundle\eZAlgoliaSearchEngine\NovaEzAlgoliaSearchEngine::class => ['all' => true],
];
```

### Add routes

```yaml
_novaezalgoliasearchengine_routes:
    resource: '@NovaEzAlgoliaSearchEngine/Resources/config/routing.yaml'
```

### ENV variables

The `SEARCH_ENGINE` environment variable  should be set to `algolia`

### Configuration

```yaml
nova_ezalgoliasearchengine:
    system:
        default:
            index_name_prefix: PREFIX
            app_id: APPLICATION_ID
            api_secret_key: ADMIN_API_KEY
            api_search_only_key: SEARCH_ONLY_PAI_KEY
            license_key: "CONTACT NOVACTIVE: direction.technique@novactive.com to get your trial"

webpack_encore:
    builds:
        nova_ezalgolia: "%kernel.project_dir%/public/assets/nova_ezalgolia/build"

framework:
    assets:
        packages:
            nova_ezalgolia:
                json_manifest_path: '%kernel.project_dir%/public/assets/nova_ezalgolia/build/manifest.json'

```

> The Algolia Application should be created on https://www.algolia.com/ to retrieve the Application ID and the API secret keys. They can be found on the **API Keys** page of the Algolia dashboard.

After having installed the package the following command should be run to init the Indexes on Algolia and set up the search attributes, sort indexes and facets:

```bash
bin/console nova:ez:algolia:indexes:setup
```

## Usage

### Query Criterions

All the Criterions created inside the eZ Queries as a filter or query field are transformed into the Algolia filter string like 

`doc_type_s:content AND (content_type_identifier_s:"article")`

#### Limitations on Criterions

A few of the main [Criterions](https://doc.ezplatform.com/en/master/guide/search/search_criteria_reference/) are not implemented yet:

- Sibling (coming soon)
- ObjectStateIdentifier (coming soon)
- UserEmail (coming soon)
- UserId (coming soon)
- UserLogin (coming soon)
- IsUserBased (coming soon)
- IsUserEnabled (coming soon)
- MapLocationDistance

The User related criterions are not implemented yet because most of them are either included in the UserMetaData Criterion.

The **MapLocationDistance** Criterion is not implemented because the Algolia geo location filter option doesn't allow uss to manage multiple fields of this type within the same document.
Filtering by location can be done using the specific request options of the Algolia Search method.
Here is the example:

```php
    $query->setRequestOption('aroundLatLng', '37.7512306, -122.4584587');
    $query->setRequestOption('aroundRadius', 3000);
```

> The documentation on this subject can be found [here](https://www.algolia.com/doc/guides/managing-results/refine-results/geolocation/how-to/filter-results-around-a-location/).

The **_geoloc** attribute is already included in the Algolia document by default for the contents that have the Map Location fields.

Another constraints are inside the Logical operators. They are:

- _FullText_ Criterion cannot be inside _LogicalNot_ or LogicalOr operator because it's moved to the Algoalia's query string request. Algolia works this way.
- _AND_ operator cannot be inside _LogicalNot_ criterion;
- _AND/OR_ operator cannot be inside _LogicalOr_ criterion.

You can find more info on the specific boolean filters on Algolia documentation [here](https://www.algolia.com/doc/api-reference/api-parameters/filters/#boolean-operators).

The full Algolia information about how the filtering works can be found [here](https://www.algolia.com/doc/api-reference/api-parameters/filters/).

### Sorting

Sorting with Algolia is based on top of Replicas. Each Replica is a duplicated index with specific configuration on attributes on which the documents are sorted.
The attributes that are used to generate the Replicas can be set in the `attributes_for_replicas` config parameter.

When using the eZ Query the **sortClauses** field assigned to the Query instance is converted into the Replica key.

### Reindexing

All the data (Content and Location items) are pushed to the Algolia Index using the `bin/console ezplatform:reindex` command.
All of them (except those specified in _exclude_content_types_ parameter or only those included in _include_content_types_ parameter) 
are converted to a specific format and sent to Algolia via saveObjects method.
Also each particular Content of allowed Content Type (included or not excluded) is pushed to the Index once published on Ez Platform admin dashboard.

### Front End Implementation

The Search page with `/search` url is overridden with custom Search controller implemented in the Bundle.
The specific routing configuration is used for that:

```yaml
ezplatform.search:
    path: /search
    methods: ['GET']
    defaults:
        _controller: 'Novactive\Bundle\eZAlgoliaSearchEngine\Controller\SearchController::searchAction'
```

The source code of the Front End implementation with React components can be found in the [`search.jsx`](bundle/Resources/assets/js/search.jsx) file.
All the main widgets are included there and can be used as examples of their implementation.
The information on React InstantSearch component basic installation and widgets showcases can be also found in the docs:
- [React search installation](https://www.algolia.com/doc/guides/building-search-ui/installation/react/)
- [React Search Widgets](https://www.algolia.com/doc/guides/building-search-ui/widgets/showcase/react/)

### Security Notes

To restrict the scope of an API key the Secured API keys are used. 
The Secured API key can be only generated from Search-only API key from the Algolia API keys list.
This kind of API key is used when performing the Search method and to prevent possible malicious request tweaks to impersonate another user, so it's done on the Back End side. 

In other words, based on the currernt User Permissions, this bundle queries Algolia including permission-related implicit filters to avoid data leaks.

More info [here](https://www.algolia.com/doc/guides/security/api-keys/how-to/user-restricted-access-to-data/?language=php).

> When performing the saveObjects method to create, update or delete the entries of the Ined the Admin API key is used.

### Advanced Usage


#### Exclude/Include Content Types from indexation

You can select which Content Types to include or exclude from the Index.
Use the following config parameters to exclude or include the specific content types:
- `nova_ezalgoliasearchengine.default.exclude_content_types`
- `nova_ezalgoliasearchengine.default.include_content_types`

The `include` parameter is checked first and hence has the priority.
By default all the content types are saved to the Index except **User** and **User Group**.

There are also the following parameters:
- Searchable Attributes;
- Attributes for Faceting;
- Attributes to Retrieve;
- Attributes for Replicas (used for sorting);

You can see the default list of the attributes that are sent to Algolia in the 
[Deafult Settings](https://github.com/Novactive/NovaeZAlgoliaSearchEngine/blob/master/bundle/Resources/config/default_settings.yaml).

To send all those setting to Algolia use the `bin/console nova:ez:algolia:indexes:setup` command.

#### Using the Query Factory to generate the custom queries

If you want to create more specific custom request that can be achieved with the Search Query Factory service
[`Novactive\Bundle\eZAlgoliaSearchEngine\Core\Search\SearchQueryFactory`](https://github.com/Novactive/NovaeZAlgoliaSearchEngine/blob/master/bundle/Core/Search/SearchQueryFactory.php).
When using it all the request parameters should be specified manually, i.e search term, filters, facets etc. like in the following example:

```php
    $query = $this->searchQueryFactory->create(
        'term',
        'content_type_identifier_s:"article"',
        ['doc_type_s']
    );
    $query->setRequestOption('attributesToRetrieve', ['content_name_s']);
```

The Replica can be also specified manually:
```php
    $query = $this->searchQueryFactory->create(
        '',
        'content_language_codes_ms:"eng-GB"',
    );
    $query->setReplicaByAttribute('location_id_i');
```

Then the created Query instance should be passed to one of the methods of 
[`Novactive\Bundle\eZAlgoliaSearchEngine\Core\Search\Search`](bundle/Core/Search/Search.php) service depending on the type of search:
- findContents
- findLocations
- find (for the raw search).


> There is also an event that enables you to tweak the `Query` created by the factory. `Novactive\Bundle\eZAlgoliaSearchEngine\Event\QueryCreateEvent` 

