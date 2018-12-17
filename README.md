# NovaeZEnhancedImageAssetBundle

What to be expected:
- Focus point managment
- Lazy loading
- Responsive loading
- Progressive loading


example query to change field type
```sql
UPDATE ezcontentclass_attribute ca
INNER JOIN ezcontentclass c ON c.id = ca.contentclass_id
SET data_type_string = "enhancedimage"
WHERE ca.data_type_string = "ezimage" and c.identifier="main_rubric";

UPDATE ezcontentobject_attribute oa
INNER JOIN ezcontentclass_attribute ca ON oa.contentclassattribute_id = ca.id
SET oa.data_type_string = "enhancedimage"
WHERE oa.data_type_string = "ezimage" AND ca.data_type_string="enhancedimage";
```
