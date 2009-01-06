-- add new log event for article editing
INSERT INTO Events (Id, Name, Notify, IdLanguage) VALUES (37, 'Edit article content', 'N', 1);

system php ./update_article_authors.php
