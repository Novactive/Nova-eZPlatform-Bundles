# Novactive Extra Bundle for eZ Publish Platform

----

This repository is what we call a "subtree split": a read-only copy of one directory of the main repository. 
It is used by Composer to allow developers to depend on specific bundles.

If you want to report or contribute, you should instead open your issue on the main repository: https://github.com/Novactive/Nova-eZPlatform-Bundles

Documentation is available in this repository via `.md` files but also packaged here: https://novactive.github.io/Nova-eZPlatform-Bundles/master/ExtraBundle/README.md.html

----

[![Downloads](https://img.shields.io/packagist/dt/novactive/ezextrabundle.svg?style=flat-square)](https://packagist.org/packages/novactive/ezextrabundle)
[![Latest version](https://img.shields.io/github/release/Novactive/NovaeZExtraBundle.svg?style=flat-square)](https://github.com/Novactive/NovaeZExtraBundle/releases)
[![License](https://img.shields.io/packagist/l/novactive/ezextrabundle.svg?style=flat-square)](LICENSE)

## Installation

### Step 1: Download Nova eZExtra Bundle using composer

Add the lib to your composer.json, run `composer require novactive/ezextrabundle` to refresh dependencies.

### Step 2: Enable the bundle

Then inject the bundle in the `bundles.php` of your application.

```php
    Novactive\Bundle\eZExtraBundle\NovaeZExtraBundle::class => [ 'all'=> true ],
```

### Step 3: Add the default routes

Activate the sroutes:

```yml
_novaezextraRoutes:
    resource: "@NovaeZExtraBundle/Resources/config/routing/main.yml"
```

### Step 4: Clear the cache and check

```bash
php app|ezpublish/console cache:clear --env=dev
```

Go to : */_novaezextra/dev/test*

## Documentation


### Twig Content Helper

#### eznova_parentcontent_by_contentinfo( content )

```twig
{% set content = eznova_parentcontent_by_contentinfo( content ) %}
```

> Note : you get the content of the parent on the main location

#### eznova_location_by_content( content )

```twig
{% set contentType = eznova_location_by_content( content ) %}
```

#### eznova_relationlist_field_to_content_list( fieldValue )

```twig
{% set content = eznova_relationlist_field_to_content_list( ez_field_value( content, 'internal_links' ) ) %}
```

> Note : return an array of direct linked contents by the relation objects FieldType

#### eznova_is_rich_text_really_empty(richTextFieldValue)

```twig
{% set content = eznova_is_rich_text_really_empty( ez_field_value( content, 'description' ) ) %}
```

> Note : returns true if the value of RichText field is empty excluding the tags, whitespaces and line breaks, false otherwise

### Twig Text Parsing Helper

#### ctaize

```twig
{% set ctaField | ctaize %}
```

> Note : Filter which converts the string like ezcontent://123 or ezlocation://234 to the URL of specified content or location

#### ezlinks

```twig
{% set richTextFieldValue | ezlinks %}
```

> Note : Filter which fixes the mistakes in opening/closing div tags and converts string like ezlocation://234 to the URL of specified location

#### htmldecode

```twig
{% set stringValue | htmldecode %}
```

> Note : Filter which applies html_entity_decode php function to the specified var

### Twig Image Helper

#### get_image_tag(content, fieldIdentifier, variationAlias, params)

```twig
{{ get_image_tag(content, 'thumbnail', 'card_slider') }}
```

> Generates the picture html code including images for original, retina and mobile screens specified in the image variations config like the following:

```yaml
optimized_original:
    reference: ~
    filters:
        - { name: auto_rotate }
        - { name: strip }
        - { name: geometry/scaledownonly, params: [ 200,200 ] }
optimized_original_retina:
    reference: ~
    filters:
        - { name: auto_rotate }
        - { name: strip }
        - { name: geometry/scaledownonly, params: [ 400,400 ] }
optimized_original_mobile:
    reference: ~
    filters:
        - { name: auto_rotate }
        - { name: strip }
        - { name: geometry/scaledownonly, params: [ 50,50 ] }
```

> If the placeholder dimensions are specified with empty content and variation then the placeholder image will be displayed:

```twig
{{ get_image_tag(null, 'image', '', {placeholder: {width: 300, height: 100}}) }}
```

#### get_image_url(content, fieldIdentifier, variationAlias, params)

> The same as previous but returning just an image URL instead of tag.

#### get_image_asset_content(field)

> Returns the Content by the Image Asset field. Requires the Ibexa\Contracts\Core\Repository\Values\Content\Field to be specified.

> **IMPORTANT**: The image placeholder is enabled by default but can be disabled by setting the bool value to _ENABLE_IMAGE_PLACEHOLDER_ env variable.

### Picture Controller

```twig
{{ render( controller( "eZNovaExtraBundle:Picture:alias", { "contentId": content.getField('picture').value.destinationContentId, "fieldIdentifier": "image", "alias": "large" })) }}
```

### Content/Location Helper

The goal was to mimic the old Fetch Content List

    public function contentTree( $parentLocationId, $typeIdentifiers = [], $sortClauses = [], $limit = null, $offset = 0, $additionnalCriterion );
    public function contentList( $parentLocationId, $typeIdentifiers = [], $sortClauses = [], $limit = null, $offset = 0, $additionnalCriterion );
    public function nextByAttribute( $locationId, $attributeIdentifier, $locale, $additionnalCriterions = [] );
    public function nextByPriority( $locationId, $aditionnalCriterions = [] )
    public function previousByAttribute( $locationId, $attributeIdentifier, $locale, $additionnalCriterion = [] )
    public function previousByPriority( $locationId, $additionnalCriterion = [] )
    public function getSelectionTextValue($content, $identifier)
    
> Return an array of Result

Usage:

```twig
    {% for child in children %}
        <h2>{{ ez_field_value( child.content, "title" ) }}</h2>
        {{ ez_render_field( child.content, "overview" ) }}
        <a href="{{ path( "ez_urlalias", { "locationId" : child.content.contentInfo.mainLocationId } ) }}">{{ "Learn more" | trans() }}</a>
    {% endfor %}
```

### Children Provider

Simply inject the children ( and potentially other things on a view Full )

Add your provider in a folder of your bundle

```yaml
Project\Bundle\GeneralBundle\ChildrenProvider\YOUCONTENTIDENTIFIERPROVIDERCLASS:
    tags:
        -  { name: novactive.ezextra.children.provider, contentTypeIdentifier: YOUCONTENTIDENTIFIER }
```

You class YOUCONTENTIDENTIFIERPROVIDERCLASS must extend Novactive\Bundle\eZExtraBundle\EventListener\Type

After you need to create a method for each view you display if you want to get children in your template
The goal is to have children on each view.

Ex:

```php
namespace Yoochoose\Bundle\GeneralBundle\ChildrenProvider;
use Novactive\Bundle\eZExtraBundle\EventListener\Type;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
class PersonalizationEngine extends Type
{
    //its also use as default to get the full view children
    public function getChildren($viewParameters, SiteAccess $siteAccess = null)
    {
        return $this->contentHelper->contentList( $this->location->id, [ 'article' ], array( new Query\SortClause\Location\Priority( Query::SORT_ASC ) ), 10);
    }
    
    public function getLineChildren( $viewParameters )
    {
        ...
    }
}
```

### RepositoryAware helper (trait)

```php
    public function loadReverseRelations(ContentInfo $contentInfo, int $offset = 0, int $limit = -1): RelationList
```
Returns the list of reverse relations (RelationList) of the specified ContentInfo

### RouterAware helper (trait)

    public function generateRouteLocation(Location $location): string
    public function generateRouteContent(Content $content): string
    public function generateRouteWrapper(Wrapper $wrapper): string
    
The trait that allows to get the Route by location, content or Wrapper object.

### ViewMatcher

This allows you to specify different ez views for the same content type but with different values of particular field.

> **IMPORTANT**: By default the field name is set to **_matcher_** but can be rewritten by specifying it in the _**VIEW_MATCHER_FIELD_IDENTIFIER**_ env variable.

Then for example if you set the **business** value to the field that is set to identify the view (**matcher** by default) inside the **Article** Content Type then another template can be defined for that using the following config:

```yaml
article_business:
    template: '@ezdesign/full/article_business.html.twig'
    match:
        Identifier\ContentType: [ 'article' ]
        '@Novactive\Bundle\eZExtraBundle\Core\ViewMatcher\ContentTypeField': 'business'
```