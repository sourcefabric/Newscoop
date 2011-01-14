-- ticket #2467 - LiveUser as new user privileges and authentication system
ALTER TABLE `liveuser_users` CHANGE fk_user_type fk_user_type INTEGER UNSIGNED;
