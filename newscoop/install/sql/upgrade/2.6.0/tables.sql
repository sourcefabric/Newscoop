-- fix for bug 2272 (Database create error when character set is UTF8)
ALTER TABLE ArticleTypeMetadata CHANGE COLUMN type_name type_name VARCHAR(166) NOT NULL;
ALTER TABLE ArticleTypeMetadata CHANGE COLUMN field_name field_name VARCHAR(166) NOT NULL;

ALTER TABLE TopicFields CHANGE COLUMN ArticleType ArticleType VARCHAR(166) NOT NULL;
ALTER TABLE TopicFields CHANGE COLUMN FieldName FieldName VARCHAR(166) NOT NULL;
