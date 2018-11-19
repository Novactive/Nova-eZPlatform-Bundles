# Custom meta fulltext field

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
