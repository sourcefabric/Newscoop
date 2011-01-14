<?php
// rebuild search-table
// this script rebuilds the search-table

// this needs some time, please make sure that its really needed
// i.e. in case of errors, required updates etc.

// it only works with the mysql/mysqli-layer.

// YOU NEED TO MOVE THIS SCRIPT TO YOUR PHORUM-DIRECTORY

define('phorum_page', 'rebuild_search_table');

if(!file_exists('./common.php')) {
    echo "You didn't move this script to your phorum-directory!\n";
    exit();
}

include './common.php';

if (! ini_get('safe_mode')) {
    set_time_limit(0);
    ini_set("memory_limit","64M");
}

echo "Rebuilding search-table ...\n";

$sql=array();
$sql[]="truncate {$PHORUM['search_table']}";
$sql[]="insert into {$PHORUM['search_table']} (message_id,search_text,forum_id) select message_id, concat(author, ' | ', subject, ' | ', body), forum_id from {$PHORUM['message_table']}";

phorum_db_run_queries($sql);

flush();
echo "Rebuilding search-table finished successfully if no errors were logged above.\n";


?>
