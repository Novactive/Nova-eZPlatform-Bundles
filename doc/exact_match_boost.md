# Exact matches boosting

To allow boosting content where the search is exactly matched (accent, case, ...) you need to add the following settings to your solr schema.xml

Add a new fieldtyp "rawtext", copy of the "text" fieldtype and remove the filter with are supposed to modify the text :
* LowerCaseFilterFactory
* FrenchLightStemFilterFactory
* ASCIIFoldingFilterFactory
* ...

Example for the french language
```xml
<!-- French -->
<fieldType name="text" class="solr.TextField" positionIncrementGap="100">
<analyzer>
    <tokenizer class="solr.StandardTokenizerFactory"/>
    <filter class="solr.LowerCaseFilterFactory"/>
    <!-- removes l', etc -->
    <filter class="solr.ElisionFilterFactory" ignoreCase="true" articles="contractions_fr.txt"/>
    <filter class="solr.WordDelimiterFilterFactory"
            generateWordParts="1"
            generateNumberParts="1"
            catenateWords="0"
            catenateNumbers="0"
            catenateAll="0"
            preserveOriginal="1"
    />
    <filter class="solr.StopFilterFactory" ignoreCase="true" words="stopwords_fr.txt" format="snowball" />
    <filter class="solr.FrenchLightStemFilterFactory"/>
    <!-- less aggressive: <filter class="solr.FrenchMinimalStemFilterFactory"/> -->
    <!-- more aggressive: <filter class="solr.SnowballPorterFilterFactory" language="French"/> -->
</analyzer>
</fieldType>

<fieldType name="rawtext" class="solr.TextField" positionIncrementGap="100">
<analyzer>
    <tokenizer class="solr.StandardTokenizerFactory"/>
    <!-- removes l', etc -->
    <filter class="solr.ElisionFilterFactory" ignoreCase="true" articles="contractions_fr.txt"/>
    <filter class="solr.WordDelimiterFilterFactory"
            generateWordParts="1"
            generateNumberParts="1"
            catenateWords="0"
            catenateNumbers="0"
            catenateAll="0"
            preserveOriginal="1"
    />
    <filter class="solr.StopFilterFactory" ignoreCase="true" words="stopwords_fr.txt" format="snowball" />
</analyzer>
</fieldType>
```

And lastly, add the following dynamic and copy field declaration :
```xml
<dynamicField name="*_t_raw" type="rawtext" indexed="true" stored="true" multiValued="true" omitNorms="false"/>
<copyField source="*_t" dest="*_t_raw"/>
```
