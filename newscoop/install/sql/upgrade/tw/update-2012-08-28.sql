ALTER TABLE  `plugin_debate_vote` CHANGE  `fk_user_id`  `fk_user_id` INT( 10 ) UNSIGNED NULL;
ALTER TABLE  `plugin_debate_vote` ADD  `device_id` VARCHAR( 80 ) NULL AFTER  `fk_user_id`;
ALTER TABLE  `plugin_debate_vote` ADD UNIQUE (`fk_debate_nr` , `device_id`);
