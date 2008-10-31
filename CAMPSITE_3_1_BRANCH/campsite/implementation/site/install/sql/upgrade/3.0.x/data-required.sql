-- Create object types
INSERT INTO ObjectTypes (name) VALUES('article');

system php ./create_object_types_description.php
-- Sets section permissions 
system php ./upgrade_section_perms.php
-- Removes InitializeTemplateEngine right
system php ./upgrade_liveuser_rights.php
