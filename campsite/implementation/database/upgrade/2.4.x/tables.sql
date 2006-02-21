-- Fix for ticket:1605 ... when lots of images are linked to articles,
-- the queries for fetching images become unbearably slow.
ALTER  TABLE  `ArticleImages`  ADD  INDEX  `IdImage` (  `IdImage`  ) ;

-- add IdLanguage column to SubsSections table
ALTER TABLE SubsSections DROP PRIMARY KEY;
ALTER TABLE SubsSections ADD COLUMN IdLanguage int(10) NOT NULL DEFAULT 0 AFTER SectionNumber;
ALTER TABLE SubsSections ADD PRIMARY KEY (IdSubscription, SectionNumber, IdLanguage);

-- update the subscription fields in the publications table
ALTER TABLE Publications DROP COLUMN PayTime;
ALTER TABLE Publications ADD COLUMN UnitCostAllLang float(10, 2) unsigned NOT NULL DEFAULT 0 AFTER UnitCost;
UPDATE Publications SET UnitCostAllLang = UnitCost;

-- add settings option
INSERT INTO UserConfig(`fk_user_id`, `varname`, `value`) VALUES (0, 'KeywordSeparator', ',');

-- Run the upgrade script
system php ./upgrade_user_perms.php
