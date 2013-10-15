
-- Insert into articletypemetadata
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`)
SELECT 'newswire',  'DataLink', (MAX(field_weight) + 1), 0,  0,  NULL, 'text', NULL, 0, 255 FROM `ArticleTypeMetadata` WHERE `type_name` = 'newswire';
