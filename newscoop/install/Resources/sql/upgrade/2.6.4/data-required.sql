-- upgrade sync users permission
system php ./upgrade_user_perms.php
-- synchronize phorum users with Users table
system php ./sync_phorum_users.php
