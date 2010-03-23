-- To know what campsite user matches the corresponding phorum user
ALTER TABLE `phorum_users` ADD `fk_campsite_user_id` INT UNSIGNED UNIQUE AFTER user_id;
