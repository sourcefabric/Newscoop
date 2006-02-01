-- Fix for ticket:1605 ... when lots of images are linked to articles,
-- the queries for fetching images become unbearably slow.
ALTER  TABLE  `ArticleImages`  ADD  INDEX  `IdImage` (  `IdImage`  ) ;

-- Run the upgrade script
system php ./upgrade_user_perms.php
