-- Create object types
INSERT INTO ObjectTypes (name) VALUES('article');

system php ./create_object_type_description.php

-- Sets section permissions 
system php ./upgrade_section_perms.php
