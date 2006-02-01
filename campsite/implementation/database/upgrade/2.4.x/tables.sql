-- Fix for ticket:1605 ... when lots of images are linked to articles,
-- the queries for fetching images become unbearably slow.
ALTER  TABLE  `ArticleImages`  ADD  INDEX  `IdImage` (  `IdImage`  ) ;

INSERT INTO UserConfig(`fk_user_id`, `varname`, `value`) VALUES (0, 'KeywordSeparator', ',');

-- Run the upgrade script
system php ./upgrade_user_perms.php
