<?php
/*
TODO:
ImplyRight
*/
require_once 'config.inc.php';
include_once 'Var_Dump.php';
Var_Dump::displayInit(
    array('display_mode' => 'XHTML_Text'),
    array('mode' => 'normal', 'offset' => 4)
);
?>

<html>
<style>
    a {
        color: #006600;
        text-decoration: none;
    }

    a:visisted {
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }
    /* style for XHTML_Text */
    table.var_dump          { border-collapse:separate; border:1px solid black; border-spacing:0; }
    table.var_dump tr       { color:#006600; background:#F8F8F8; vertical-align:top; }
    table.var_dump tr.alt   { color:#006600; background:#E8E8E8; }
    table.var_dump th       { padding:4px; color:black; background:#CCCCCC; text-align:left; }
    table.var_dump td       { padding:4px; }
    table.var_dump caption  { caption-side:top; color:white; background:#339900; }
    table.var_dump i        { color: #000000; background: transparent; font-style: normal; }

    /* style for XHTML_Text */
    pre.var_dump            { line-height:1.8em; }
    pre.var_dump span.type  { color:#006600; background:transparent; }
    pre.var_dump span.value { padding:2px; color:#339900; background:#F0F0F0; border: 1px dashed #CCCCCC; }

</style>
<body>
<form action="?" method="get">
 <select name="storage">
<?php
    foreach ($backends as $backend => $row) {
       $selected = $storage == $backend ? 'selected="selected"' : '';
        echo '<option value="'.$backend.'" '.$selected.'>'.$backend.'</option>';
    }
?>
 </select>
 <input type="submit" id="go" value="Go!" />
 Make sure you read README in the examples root folder to setup the database.
</form>
<?php
$qstring = array_key_exists('QUERY_STRING', $_SERVER) && !array_key_exists('del', $_GET) ? '?'.$_SERVER['QUERY_STRING'] : '';

echo '
<a href="Application.php'.$qstring.'">Application</a> |
<a href="Area.php'.$qstring.'">Area</a> |
<a href="Rights.php'.$qstring.'">Rights</a> |
<a href="ImplyRights.php'.$qstring.'">ImplyRights</a> |
<a href="User.php'.$qstring.'">User</a> |
<a href="UserRights.php'.$qstring.'">UserRights</a> |
<a href="Group.php'.$qstring.'">Group</a> |
<a href="UserGroup.php'.$qstring.'">UserGroup</a> |
<a href="GroupRights.php'.$qstring.'">GroupRights</a> |
<a href="Subgroups.php'.$qstring.'">Subgroups</a> |
<a href="Area_Admin_Areas.php'.$qstring.'">Area Admin Areas</a> |
<a href="Translation.php'.$qstring.'">Translation</a> |
<a href="OutputRightsConstants.php'.$qstring.'">OutputRightsConstants</a> |
<a href="test.php'.$qstring.'">Test</a><br />';
?>
So that these test will run you have to have <a href="http://pear.php.net/package/Var_Dump">Var_Dump</a> installed<br /><br />
<?php
if (array_key_exists('del', $_GET)) {
    $db->expectError(MDB2_ERROR_NOSUCHTABLE);
    $db->query('DELETE FROM liveuser_applications');
    $db->query('DROP TABLE liveuser_applications_seq');
    $db->query('DELETE FROM liveuser_area_admin_areas');
    $db->query('DELETE FROM liveuser_areas');
    $db->query('DROP TABLE liveuser_areas_seq');
    $db->query('DELETE FROM liveuser_group_subgroups');
    $db->query('DELETE FROM liveuser_grouprights');
    $db->query('DELETE FROM liveuser_groups');
    $db->query('DROP TABLE liveuser_groups_seq');
    $db->query('DELETE FROM liveuser_groupusers');
    $db->query('DROP TABLE liveuser_groupusers_seq');
    $db->query('DELETE FROM liveuser_perm_users');
    $db->query('DROP TABLE liveuser_perm_users_seq');
    $db->query('DELETE FROM liveuser_right_implied');
    $db->query('DELETE FROM liveuser_rights');
    $db->query('DROP TABLE liveuser_rights_seq');
    $db->query('DELETE FROM liveuser_userrights');
    $db->query('DROP TABLE liveuser_userrights_seq');
    $db->query('DELETE FROM liveuser_users');
    $db->query('DROP TABLE liveuser_users_seq');
    $db->query('DELETE FROM liveuser_translations');
    $db->query('DROP TABLE liveuser_translations_seq');
    $db->popExpect();
    echo 'Reseted the database';
    exit;
} else {
    echo '<a href="?del=1">Reset the database</a><br /><br />';
}
?>
</body>
</html>