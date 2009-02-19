-- add new log event for article editing
INSERT INTO Events (Id, Name, Notify, IdLanguage) VALUES (37, 'Edit article content', 'N', 1);

INSERT INTO `liveuser_rights` VALUES ((SELECT id + 1 FROM `liveuser_rights_right_id_seq`), 0, 'EditorStatusBar', 1);
UPDATE `liveuser_rights_right_id_seq` set id = id + 1;

-- do not set the article author to the article owner 
-- system php ./update_article_authors.php
