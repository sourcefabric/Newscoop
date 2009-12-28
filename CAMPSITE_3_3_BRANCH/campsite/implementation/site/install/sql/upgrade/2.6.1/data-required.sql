-- add new event: to be used when synchronizing campsite and phorum users
INSERT INTO Events (Id, Name, Notify, IdLanguage) VALUES (161, 'Sync campsite and phorum users', 'N', 1);

-- create the corresponding phorum users for each campsite subscriber
system php ./upgrade_phorum_users.php
