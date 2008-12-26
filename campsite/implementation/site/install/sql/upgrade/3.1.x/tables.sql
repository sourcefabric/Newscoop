ALTER TABLE `Requests` ADD INDEX `requests_session_idx`(`session_id`);
ALTER TABLE `Requests` ADD INDEX `requests_object_idx`(`object_id`);
ALTER TABLE `Requests` DROP COLUMN `request_count`;
ALTER TABLE `Requests` DROP COLUMN `last_request_time`;
ALTER TABLE `Requests` ADD COLUMN `last_stats_update` DATETIME NOT NULL;


CREATE TABLE `RequestStats` (
  `object_id` INTEGER  NOT NULL,
  `date` DATE  NOT NULL,
  `hour` INTEGER  NOT NULL,
  `request_count` INTEGER  NOT NULL DEFAULT 0,
  PRIMARY KEY (`object_id`, `date`, `hour`),
  INDEX `stats_object_idx`(`object_id`),
  INDEX `stats_object_date_idx`(`object_id`, `date`),
  INDEX `stats_object_hour_idx`(`object_id`, `hour`)
);
