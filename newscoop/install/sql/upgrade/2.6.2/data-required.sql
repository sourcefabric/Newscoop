-- add new event: to be used to log system preferences changes 
INSERT INTO Events (Id, Name, Notify, IdLanguage) VALUES (171, 'Change system preferences', 'N', 1);

-- create the corresponding phorum users for each campsite subscriber
system php ./upgrade_phorum_users.php
