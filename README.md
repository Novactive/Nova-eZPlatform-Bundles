# Novactive Extra Bundle for eZ Publish Platform

## Installation

### Step 1: Download Nova eZExtra Bundle using composer

Add NovaeZExtraBundle in your composer.json: 

``` js
{
    "require": {
        "novactive/ezextrabundle": "dev-master"
    }
}
```

Now tell composer to download the bundle by running the command:

``` bash
$ composer.phar update novactive/ezextrabundle
```

### Step 2: Enable the bundle

#### Enable the bundle in the kernel:

``` php
<?php
// ezpublish/EzPublishKernel.php

public function registerBundles() {
    $bundles = array(
        // ...
		new Novactive\Bundle\eZExtraBundle\NovaeZExtraBundle(),
    );
}
```

### Step 3: Add the default routes

Activate the "dev" routes:

``` yml
_novaezetraRoutesDev:
    resource: "@NovaeZExtraBundle/Resources/config/routing/dev.yml"
```

Activate the "prod" routes:

``` yml
_novaezextraRoutes:
    resource: "@NovaeZExtraBundle/Resources/config/routing/main.yml"
```

### Step 4: Clear the cache and check

``` bash
php app|ezpublish/console cache:clear --env=dev
```

Go to : */_novaezextra/dev/test*

## Documentation


### Twig Helper


#### eznova_content_by_contentinfo( location.contentInfo )

``` twig
{% set content = eznova_content_by_contentinfo( location.contentInfo ) %}
```

#### eznova_contenttype_by_content( content )

``` twig
{% set contentType = eznova_contenttype_by_content( content ) %}
```

#### eznova_parentcontent_by_contentinfo( content )

``` twig
{% set contentType = eznova_parentcontent_by_contentinfo( content ) %}
```

> Note : you get the content of the parent on the main location

#### eznova_location_by_content( content )

``` twig
{% set contentType = eznova_location_by_content( content ) %}
```

#### eznova_location_by_locationId( locationId )

``` twig
{% set contentType = eznova_location_by_locationId( locationId ) %}
```

#### eznova_relation_field_to_content( fieldValue )

``` twig
{% set content = eznova_relation_field_to_content( ez_field_value( content, 'internal_link' ) ) %}
```

> Note : return the direct linked content by the relation object FieldType

#### eznova_relationlist_field_to_content_list( fieldValue )

``` twig
{% set content = eznova_relationlist_field_to_content_list( ez_field_value( content, 'internal_links' ) ) %}
```

> Note : return an array of direct linked contents by the relation objects FieldType

### Picture Controller

``` twig
{{ render( controller( "eZNovaExtraBundle:Picture:alias", { "contentId": content.getField('picture').value.destinationContentId, "fieldIdentifier": "image", "alias": "large" })) }}
```

### Content/Location Helper

The goal was to mimic the old Fetch Content List

    public function contentList( $parentLocationId, $typeIdentifiers = [], $sortClauses = [], $limit = null, $offset = 0 );
    public function nextByAttribute( $locationId, $attributeIdentifier );
    public function nextByPriority( $locationId )
    public function previousByAttribute( $locationId, $attributeIdentifier )
    public function previousByPriority( $locationId )
    
> Return an array of Result

Usage:

```twig
    {% for child in children %}
        <h2>{{ ez_field_value( child.content, "title" ) }}</h2>
        {{ ez_render_field( child.content, "overview" ) }}
        <a href="{{ path( "ez_urlalias", { "locationId" : child.content.contentInfo.mainLocationId } ) }}">{{ "Learn more" | trans() }}</a>
    {% endfor %}
```


### Search Helper

#### Content/Location Search Helper

```php
        $searchStructure = new SearchStructure();
        $contentTypeService = $this->getRepository()->getContentTypeService();
        $searchStructure
            ->setLimit( 10 )
            ->setFacets( $this->getSearchFacets() )
            ->setContentTypesIds(
                [
                    $contentTypeService->loadContentTypeByIdentifier( 'identifier1' )->id,
                    $contentTypeService->loadContentTypeByIdentifier( 'identifier2' )->id
                ]
            )
            ->setPage( $page );
            
        $results = $this->get( 'novactive.ezextra.search.helper' )->search( $searchStructure );
```

> Return an array of Result

#### Paginator

Witht the search you can also use the Paginator

```php

            $adapter    = new SearchAdapter( $this->get( 'novactive.ezextra.search.helper' ), $searchStructure );
            $pagerFanta = new Pagerfanta( $adapter );
            $pagerFanta->setMaxPerPage( $searchStructure->getLimit() );
            $pagerFanta->setCurrentPage( $page );
```

#### Search Form

The bundle provide you a simple way to integrate the SearchStructure in a Symfony Form

```php
        $pagerFanta = null;
        $searchStructure = new SearchStructure();
        $contentTypeService = $this->getRepository()->getContentTypeService();
        $searchStructure
            ->setLimit( 10 )
            ->setFacets( $this->getSearchFacets() )
            ->setContentTypesIds(
                [
                    $contentTypeService->loadContentTypeByIdentifier( 'identifier' )->id
                ]
            )
            ->setPage( $page );

        $form = $this->get( 'form.factory' )->createNamed( '', 'novactive_ezextra_simple_search', $searchStructure );
        $form->handleRequest( $request );
        if ( $option !== null )
        {
            $searchStructure->addFilters( [ "attr_options_lk:\"{$option}\"" ] );
        }

        if ( $form->isValid() )
        {
            $adapter    = new SearchAdapter( $this->get( 'novactive.ezextra.search.helper' ), $searchStructure );
            $pagerFanta = new Pagerfanta( $adapter );
            $pagerFanta->setMaxPerPage( $searchStructure->getLimit() );
            $pagerFanta->setCurrentPage( $page );
        }

        return [
            'form' => $form->createView(),
            'pager' => $pagerFanta,
            'searchStructure' => $searchStructure,
            'option' => $option
        ];
    }

```


### Children Provider

Simply inject the children ( and potentially other things on a view Full )

Add your provider

```yml
project.home_page.children.provider:
    class: Project\Bundle\GeneralBundle\ChildrenProvider\YOUCONTENTIDENTIFIERPROVIDERCLASS
    parent: novactive.ezextra.abstract.children.provider
    tags:
        -  { name: novactive.ezextra.children.provider, contentTypeIdentifier: YOUCONTENTIDENTIFIER }
```

You class YOUCONTENTIDENTIFIERPROVIDERCLASS must extend Novactive\Bundle\eZExtraBundle\EventListener\Type

Ex:

```php
namespace Yoochoose\Bundle\GeneralBundle\ChildrenProvider;
use Novactive\Bundle\eZExtraBundle\EventListener\Type;
use eZ\Publish\API\Repository\Values\Content\Query;
class PersonalizationEngine extends Type
{
    public function getChildren( $viewParameters )
    {
        return $this->contentHelper->contentList( $this->location->id, [ 'article' ], array( new Query\SortClause\Location\Priority( Query::SORT_ASC ) ), 10);
    }
}
```



