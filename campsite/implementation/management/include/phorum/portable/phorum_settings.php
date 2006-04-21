<?php
// needed to really load the alternate db-config in common.php
define("PHORUM_WRAPPER",1);

// set the Phorum install dir
$PHORUM_DIR="/www/dev.phorum/phorum5";

// set the databse settings for this Phorum Install
$PHORUM_ALT_DBCONFIG=array(

   "type"          =>  "mysql",
   "name"          =>  "phorum",
   "server"        =>  "localhost",
   "user"          =>  "phorum",
   "password"      =>  "phorum",
   "table_prefix"  =>  "phorum_portable"

);

// We have to alter the urls a little
function phorum_custom_get_url ($page, $query_items, $suffix)
{
    $PHORUM=$GLOBALS["PHORUM"];

    $url = "$PHORUM[http_path]/phorum.php?$page";

    if(count($query_items)) $url.=",".implode(",", $query_items);

    if(!empty($suffix)) $url.=$suffix;

    return $url;
}

?>