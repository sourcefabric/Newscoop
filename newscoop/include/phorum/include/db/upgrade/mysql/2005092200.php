<?php
if(!defined("PHORUM_ADMIN")) return;

// Check if all the tables from version 2005091400 were created,
// before dropping the old private messages table.

$old_table = "{$PHORUM['DBCONFIG']['table_prefix']}_private_messages";

$cid=phorum_db_mysql_connect();

$check_tables = array(
   $PHORUM["pm_messages_table"] => 1,
   $PHORUM["pm_folders_table"]  => 1,
   $PHORUM["pm_xref_table"]     => 1,
);

$res = mysql_query("show tables");
if ($res) {
    while (($row = mysql_fetch_array($res))) {
        if (isset($check_tables[$row[0]])) {
            unset($check_tables[$row[0]]);
        }
    }
}

if (count($check_tables)) { ?>
    <br/>
    <b>Warning: database upgrade 2005091400 does not seem to have
    completed successfully. The old style private messages table
    <?php print $old_table ?> will be kept for backup. <?php
} else {
    $upgrade_queries[] = "DROP TABLE $old_table"; 
}

?>
