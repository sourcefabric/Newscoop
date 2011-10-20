<?php

require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'SystemPref.php');

camp_load_translation_strings('plugin_newsimport');

$is_admin = false;
// User role depend on path to this file.
if (strpos($call_script, '/newsimport/admin/') !== false && $g_user->hasPermission('EditSystem-Preferences')) {
    $is_admin = true;
}

// Check permissions
if (!$is_admin) {
    camp_html_display_error(getGS('You do not have the right to manage NewsImport.'));
    exit;
}

$conf_feeds = false;
$auth_feeds = false;

$plugin_path = dirname(dirname(dirname(dirname(__FILE__))));

$feed_conf_path = $GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'conf'.DIRECTORY_SEPARATOR.'newsimport'.DIRECTORY_SEPARATOR.'news_feeds_conf.php';
if (!is_file($feed_conf_path)) {
    $feed_conf_path = $plugin_path . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'news_feeds_conf_inst.php';
}
$feed_auth_path = $plugin_path . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'default_access.php';

if (is_file($feed_conf_path)) {
    require($feed_conf_path);
    $conf_feeds = true;
}
if (is_file($feed_auth_path)) {
    require($feed_auth_path);
    $auth_feeds = true;
}


if (Input::Get('save')) {
    if (!SecurityToken::isValid()) {
        camp_html_display_error(getGS('Invalid security token!'));
        exit;
    }

/*
    $f_newsimport_command_token = Input::Get('f_command_token', 'string');
    if (!empty($f_newsimport_command_token)) {
        SystemPref::Set('NewsImportCommandToken', $f_newsimport_command_token);
    }
*/

    if ($conf_feeds) {
        foreach ($event_data_sources as $feed_key => $feed_conf) {
            $feed_key = base64_encode($feed_key);

            $f_newsimport_images_local = Input::Get('f_images_local-'.$feed_key, 'string');
            if (!empty($f_newsimport_images_local)) {
                SystemPref::Set('NewsImportImagesLocal:' . $feed_key, $f_newsimport_images_local);
            }

            $f_publication_id = Input::Get('f_publication_id-'.$feed_key, 'string');
            if ('' != ('' . $f_publication_id)) {
                SystemPref::Set('NewsImportPublicationId:' . $feed_key, $f_publication_id);
            }

            $f_issue_number = Input::Get('f_issue_number-'.$feed_key, 'string');
            if ('' != ('' . $f_issue_number)) {
                SystemPref::Set('NewsImportIssueNumber:' . $feed_key, $f_issue_number);
            }

            $f_section_number = Input::Get('f_section_number-'.$feed_key, 'string');
            if ('' != ('' . $f_section_number)) {
                SystemPref::Set('NewsImportSectionNumber:' . $feed_key, $f_section_number);
            }

        }
    }


/*
    $f_newsimport_http_auth_usr = Input::Get('f_http_auth_usr', 'string');
    SystemPref::Set('NewsImportHttpAuthUser', $f_newsimport_http_auth_usr);
    $f_newsimport_http_auth_pwd = Input::Get('f_http_auth_pwd', 'string');
    SystemPref::Set('NewsImportHttpAuthPwd', $f_newsimport_http_auth_pwd);
*/

    camp_html_add_msg(getGS('NewsImport preferences updated.'), 'ok');
}

echo camp_html_breadcrumbs(array(
    array(getGS('Plugins'), $Campsite['WEBSITE_URL'] . '/admin/plugins/manage.php'),
    array(getGS('NewsImport'), ''),
    array('', ''),
));

camp_html_display_msgs();
?>
<p></p>

<form name="newsimport_prefs" method="post">
<?php echo SecurityToken::FormParameter(); ?>

<table border="0" width="600" cellspacing="0" cellpadding="0" class="box_table">
<tr>
  <td valign="top" colspan="2">
    <p><?php putGS('NewsImport - news events import.'); ?></p>
  </td>
</tr>

</table>


<?php

if ($conf_feeds) {

    foreach ($event_data_sources as $feed_key => $feed_conf) {
        echo '<table border="0" width="600" cellspacing="0" cellpadding="0" class="box_table">';

        echo '<tr><td width="200">Feed: </td><td>' . $feed_key . '</td></tr>';

        $feed_key = base64_encode($feed_key);

        $one_feed_images_local = $feed_conf['images_local'];
        $one_feed_images_local_sys_pref = SystemPref::Get('NewsImportImagesLocal:' . $feed_key);
        if (!empty($one_feed_images_local_sys_pref)) {
            if ('Y' == $one_feed_images_local_sys_pref) {
                $one_feed_images_local = true;
            }
            else {
                $one_feed_images_local = false;
            }
        }

        echo '<tr><td>Download images: </td><td>';
        echo '<input type="radio" name="f_images_local-'.$feed_key.'" value="Y" ' . ($one_feed_images_local ? 'checked' : '') . '/>' . getGS('Yes');
        echo '<input type="radio" name="f_images_local-'.$feed_key.'" value="N" ' . ($one_feed_images_local ? '' : 'checked') . '/>' . getGS('No');
        echo '</td></tr>';

        $one_feed_publication = $feed_conf['publication_id'];
        $one_feed_publication_sys_pref = SystemPref::Get('NewsImportPublicationId:' . $feed_key);
        if (!empty($one_feed_publication_sys_pref)) {
            $one_feed_publication = $one_feed_publication_sys_pref;
        }

        echo '<tr><td>Publication id: </td><td><input name="f_publication_id-'.$feed_key.'" value="' . ($one_feed_publication) . '" size="3" /></td></tr>';

        $one_feed_issue = $feed_conf['issue_number'];
        $one_feed_issue_sys_pref = SystemPref::Get('NewsImportIssueNumber:' . $feed_key);
        if (!empty($one_feed_issue_sys_pref)) {
            $one_feed_issue = $one_feed_issue_sys_pref;
        }

        echo '<tr><td>Issue number: </td><td><input name="f_issue_number-'.$feed_key.'" value="' . ($one_feed_issue) . '" size="3" /></td></tr>';

        $one_feed_section = $feed_conf['section_number'];
        $one_feed_section_sys_pref = SystemPref::Get('NewsImportSectionNumber:' . $feed_key);
        if (!empty($one_feed_section_sys_pref)) {
            $one_feed_section = $one_feed_section_sys_pref;
        }

        echo '<tr><td>Section number: </td><td><input name="f_section_number-'.$feed_key.'" value="' . ($one_feed_section) . '" size="3" /></td></tr>';

        echo '</table>';
    }

}


?>

<!--
<table border="0" width="600" cellspacing="0" cellpadding="0" class="box_table">
<?php

    $web_http_auth_usr = SystemPref::Get('NewsImportHttpAuthUser');
    $web_http_auth_pwd = SystemPref::Get('NewsImportHttpAuthPwd');

    echo '<tr><td colspan="2" align="left">' . 'Http authentication if used' . ': </td>';
    echo '<tr><td width="200">' . 'web user' . ': </td>';
    echo '<td><input name="f_http_auth_usr" value="' . htmlspecialchars($web_http_auth_usr) . '" /></td></tr>';
    echo '<tr><td width="200">' . 'web pwd' . ': </td>';
    echo '<td><input name="f_http_auth_pwd" value="' . htmlspecialchars($web_http_auth_pwd) . '" /></td></tr>';

?>
</table>
-->

<!--
<table border="0" width="600" cellspacing="0" cellpadding="0" class="box_table">
<?php
if ($auth_feeds) {

    $cur_nimp_auth = SystemPref::Get('NewsImportCommandToken');
    if (!empty($cur_nimp_auth)) {
        $newsimport_default_access = $cur_nimp_auth;
    }
    echo '<tr><td width="200">' . 'Command token' . ': </td>';
    echo '<td><input name="f_command_token" value="' . htmlspecialchars($newsimport_default_access) . '" /></td></tr>';
}

?>
</table>
-->

<table border="0" width="600" cellspacing="0" cellpadding="0" class="box_table">
<tr>
  <td colspan="2" align="center" style="padding-top: 10px;">
    <input type="submit" name="save" value="<?php putGS('Save'); ?>" class="button" />
  </td>
</tr>
</table>
</form>
