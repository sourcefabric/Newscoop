<?php 
/*

This is just a simple script for updating the post-count of each user, which
is shown in the user's profile. It can be run multiple times, but should at
least be run once after a conversion from Phorum 3 to Phorum 5.

How to use?

Just copy this script to your main Phorum 5 directory and run it either
from your webbrowser or from the console. It will show only some summary
in the end, nothing more.

Depending on the number of messages and users, it may take some time.

*/


// we try to disable the execution timeout
// that command doesn't work in safe_mode :(
set_time_limit(0);

require './common.php';

// no need to change anything below this line
$sql="select user_id, count(*) as postcnt from ".$PHORUM["message_table"]." group by user_id";
$conn = phorum_db_mysql_connect();
$res = mysql_query($sql, $conn);
if ($err = mysql_error()) phorum_db_mysql_error("$err: $sql");
if(mysql_num_rows($res)) {
    $usercnt=0;
    while($row = mysql_fetch_row($res)) {
        $user=array("user_id"=>$row[0],"posts"=>$row[1]);
        phorum_user_save_simple($user);
        $usercnt++;
    }
}

print "$usercnt Users updated with their current postcounts. Done!<br>\n";

?>
