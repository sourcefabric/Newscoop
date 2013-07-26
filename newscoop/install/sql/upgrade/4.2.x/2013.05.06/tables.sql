ALTER TABLE `ArticleRendition` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `ArticleRendition` CHANGE `image_id` `image_id` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL NOT NULL;
ALTER TABLE `ArticleRendition` CHANGE `rendition_id` `rendition_id` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL NOT NULL;
ALTER TABLE `ArticleRendition` CHANGE `articleNumber` `articleNumber` INT( 11 ) NOT NULL;
ALTER TABLE `ArticleRendition` CHANGE `imageSpecs` `imageSpecs` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

ALTER TABLE `package_item` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `package_item` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT;
ALTER TABLE `package_item` CHANGE `package_id` `package_id` INT( 11 ) DEFAULT NULL;
ALTER TABLE `package_item` CHANGE `image_id` `image_id` INT( 11 ) DEFAULT NULL;
ALTER TABLE `package_item` CHANGE `offset` `offset` INT( 11 ) NOT NULL;
ALTER TABLE `package_item` CHANGE `caption` `caption` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `package_item` CHANGE `coords` `coords` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `package_item` CHANGE `video_url` `video_url` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

ALTER TABLE `plugin_debate_vote` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `plugin_debate_vote` CHANGE `id_vote` `id_vote` INT( 11 ) NOT NULL;
ALTER TABLE `plugin_debate_vote` CHANGE `fk_debate_nr` `fk_debate_nr` INT( 11 ) NOT NULL;
ALTER TABLE `plugin_debate_vote` CHANGE `fk_answer_nr` `fk_answer_nr` INT( 11 ) NOT NULL;
ALTER TABLE `plugin_debate_vote` CHANGE `fk_user_id` `fk_user_id` INT( 11 ) NOT NULL;

ALTER TABLE `rendition` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `rendition` CHANGE `name` `name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL NOT NULL;
ALTER TABLE `rendition` CHANGE `width` `width` INT( 11 ) NOT NULL;
ALTER TABLE `rendition` CHANGE `height` `height` INT( 11 ) NOT NULL;
ALTER TABLE `rendition` CHANGE `specs` `specs` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL NOT NULL;
ALTER TABLE `rendition` CHANGE `offset` `offset` INT( 11 ) NOT NULL;
ALTER TABLE `rendition` CHANGE `label` `label` VARCHAR( 80 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL NOT NULL;