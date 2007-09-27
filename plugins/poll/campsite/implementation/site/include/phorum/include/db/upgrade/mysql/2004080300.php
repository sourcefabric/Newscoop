<?php
if(!defined("PHORUM_ADMIN")) return;

// wow doing it all by hand this time :(

$cid=phorum_db_mysql_connect();
// adding the new field
mysql_query("ALTER TABLE {$PHORUM['user_newflags_table']} ADD message_id INT( 11 ) NOT NULL",$cid);
// removing old primary-key
mysql_query("ALTER TABLE {$PHORUM['user_newflags_table']} DROP PRIMARY KEY",$cid);
// adding new primary-key
mysql_query("ALTER TABLE {$PHORUM['user_newflags_table']} ADD PRIMARY KEY ( user_id , forum_id , message_id )",$cid);

// converting the newflags
$res=mysql_query("SELECT * FROM {$PHORUM['user_newflags_table']} where message_id=0",$cid);
$olduser=$GLOBALS['PHORUM']['user']['user_id'];
while($row=mysql_fetch_assoc($res)) {
    $forum=$row['forum_id'];
    $data=unserialize($row['newflags']);
    $GLOBALS['PHORUM']['user']['user_id']=$row['user_id'];
    $newdata=array();
    foreach($data as $mid1 => $mid2) {
        if(is_int($mid1)) {
            $newdata[]=array("id"=>$mid1,"forum"=>$forum);
        }
    }
    phorum_db_newflag_add_read($newdata);
    unset($data);
    unset($newdata);
}
$GLOBALS['PHORUM']['user']['user_id']=$olduser;
mysql_query("DELETE FROM {$PHORUM['user_newflags_table']} where message_id=0",$cid);

// remove old column
mysql_query("ALTER TABLE {$PHORUM['user_newflags_table']} DROP newflags",$cid);

?>
