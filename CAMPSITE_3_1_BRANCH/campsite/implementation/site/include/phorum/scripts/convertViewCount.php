<?php
/*
This script converts viewcount data from mod_viewcount to Phorum's internal viewcounter.
To use it, copy the script to your main Phorum directory (eg: /phorum5), and run it from the command line.
"php convertViewCount.php"
It should work from a web browser also, although if output buffering is enabled it may not
output anything until it is completed.

If you are enabling Phorum's internal viewcount setting, you should disable mod_viewcount.
*/
include("include/db/config.php");
mysql_connect($PHORUM["DBCONFIG"]["server"],$PHORUM["DBCONFIG"]["user"],$PHORUM["DBCONFIG"]["password"]);
mysql_select_db($PHORUM["DBCONFIG"]["name"]);
$query = "SELECT message_id, meta, viewcount FROM $PHORUM[DBCONFIG][table_prefix]_messages ORDER BY message_id DESC";
$result = mysql_query($query);
while ($row = mysql_fetch_array($result)){
        print("Converting message $row[0]\n");
        $meta = unserialize($row["meta"]);
        if (isset($meta["mod_viewcount"][$row["message_id"]])){
                $count = $row["viewcount"] + $meta["mod_viewcount"][$row["message_id"]];
        }
        else{
                $count = 0;
        }
        $query = "UPDATE $PHORUM[DBCONFIG][table_prefix]_messages SET viewcount = $count WHERE message_id = $row[message_id]";
        mysql_query($query);
}
?>
