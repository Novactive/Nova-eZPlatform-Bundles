# Novactive Extra Bundle for eZ Publish Platform

## Installation

### Step 1: Download eZNovaExtraBundle using composer

Add eZNovaExtraBundle in your composer.json: 

``` js
{
    "require": {
        "novactive/novaezextrabundle": "dev-master"
    }
}
```

Now tell composer to download the bundle by running the command:

``` bash
$ composer.phar update novactive/novaezextrabundle
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

### eznova_content_by_contentinfo( location.contentInfo )

``` twig
{% set content = eznova_content_by_contentinfo( location.contentInfo ) %}
```

### eznova_contenttype_by_content( content )

``` twig
{% set contentType = eznova_contenttype_by_content( content ) %}
```

### eznova_parentcontent_by_contentinfo( content )

``` twig
{% set contentType = eznova_parentcontent_by_contentinfo( content ) %}
```

> Note : you get the content of the parent on the main location

### eznova_location_by_content( content )

``` twig
{% set contentType = eznova_location_by_content( content ) %}
```

### eznova_relation_field_to_content( fieldValue )

``` twig
{% set content = eznova_relation_field_to_content( ez_field_value( content, 'internal_link' ) ) %}
```

> Note : return the direct linked content by the relation object FieldType

### eznova_relationlist_field_to_content_list( fieldValue )

``` twig
{% set content = eznova_relationlist_field_to_content_list( ez_field_value( content, 'internal_links' ) ) %}
```

> Note : return an array of direct linked contents by the relation objects FieldType

### Picture Controller

``` twig
{{ render( controller( "eZNovaExtraBundle:Picture:alias", { "contentId": content.getField('picture').value.destinationContentId, "fieldIdentifier": "image", "alias": "large" })) }}
```

