<?php

$cs_dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
require_once("$cs_dir/conf/database_conf.php");
require_once("$cs_dir/conf/install_conf.php");
if (!is_array($Campsite)) {
    echo "Invalid configuration file(s)";
    exit(1);
}

$db_name = $Campsite['DATABASE_NAME'];
$db_user = $Campsite['DATABASE_USER'];
$db_passwd = $Campsite['DATABASE_PASSWORD'];
$db_host = $Campsite['DATABASE_SERVER_ADDRESS'];

if (!mysql_connect($db_host, $db_user, $db_passwd)) {
    die("Unable to connect to the database.\n");
}

if (!mysql_select_db($db_name)) {
    die("Unable to use the database " . $db_name . ".\n");
}

//
// gets sections key info
//
$sql = "SELECT Number, IdPublication, NrIssue, IdLanguage FROM Sections";
if (!($res = mysql_query($sql))) {
    die("Unable to read from the database.\n");
}

//
// adds section rights to liveuser_rights table based on section number, publication, issue and language for each section
//
$sql = "SELECT MAX(right_id) AS id FROM liveuser_rights";
if (!($res2 = mysql_query($sql))) {
    die("Unable to read from the database.\n");
}
$data = mysql_fetch_array($res2, MYSQL_ASSOC);
$seq = (!$data['id']) ? 0 : $data['id'];
while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
    $seq += 1;
    $sql = "INSERT INTO liveuser_rights (right_id, area_id, right_define_name, has_implied)
            VALUES (".$seq.", 0, 'ManageSection".$row['Number']."_P".$row['IdPublication']."_I".$row['NrIssue']."_L".$row['IdLanguage']."', 1)";
    if (!($res3 = mysql_query($sql))) {
        die("Unable to write to the database.\n");
    }
}

//
// updates sequence id for the liveuser_rights table
//
if ($seq > 0) {
    $sql = "INSERT INTO liveuser_rights_right_id_seq (id) VALUES (".$seq.")";
    if (!($res = mysql_query($sql))) {
        die("Unable to write to the database.\n");
    }
}

?>
