# Custom meta field

## Fulltext fields
When defining a special configuration, it's possible to store the value of multiple content fields in a unique solr fields.

This allow for example to do full text search on multiple fields with a different boost on each field.

```yaml
ez_solr_search_extra:
    system:
        default:
            fulltext_fields:
                <field name>:
                - <content field identifier>
                - <content type identifier>/<content field identifier>
```

Format for content field are : 
* `<content field identifier>`
* `<content type identifier>/<content field identifier>`

The following example will add two fields to solr documents :
* meta_title__text_t
* meta_intro__text_t

```yaml
ez_solr_search_extra:
    system:
        default:
            fulltext_fields:
                title:
                - title
                - article/name
                intro:
                - introduction
                - heading
```

## Custom field

Work like the "Fulltext fields" but for others field types. 
This can be used to generate facets based on the values of multiple fields of multiple content types

```yaml
ez_solr_search_extra:
    system:
        default:
            custom_fields:
                <field name>:
                - <content field identifier>
                - <content type identifier>/<content field identifier>
```

The following example will add two fields to solr documents :
* title_value_s
* intro_text_t

```yaml
ez_solr_search_extra:
    system:
        default:
            custom_fields:
                title:
                - title
                - article/name
                intro:
                - introduction
                - heading
```

## Publish date field

A new date field is added to every content documents : `meta_publishdate__date_dt`

This allow to boost content based on their publish date, this mean that newer content get higher score.

The value of this field is by default set to content publish date.

However it possible to define a content field to get the value from using the following setting :

```yaml
ez_solr_search_extra:
    system:
        default:
            publishdate_fields:
                - <content field identifier>
                - <content type identifier>/<content field identifier>
```
