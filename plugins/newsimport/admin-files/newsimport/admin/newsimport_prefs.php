<?php

camp_load_translation_strings('plugin_newsimport');

// User role depend on path to this file.
if (strpos($call_script, '/newsimport/admin/') !== false && $g_user->hasPermission('plugin_newsimport_admin')) {
    $is_admin = true;
}

// Check permissions
if (!$is_admin) {
    camp_html_display_error(getGS('You do not have the right to manage NewsImport.'));
    exit;
}

if (Input::Get('save')) {
    if (!SecurityToken::isValid()) {
        camp_html_display_error(getGS('Invalid security token!'));
        exit;
    }

    // not putting it into sysprefs, since the cron job would not be able to access it
    //$f_newsimport_command_token = Input::Get('f_newsimport_command_token', 'string');
    //SystemPref::Set('Plugin_NewsImport_CommandToken', $f_newsimport_command_token);

    camp_html_add_msg(getGS('NewsImport preferences updated.'), 'ok');
}

echo camp_html_breadcrumbs(array(
    array(getGS('Plugins'), $Campsite['WEBSITE_URL'] . '/admin/plugins/manage.php'),
    array(getGS('NewsImport'), ''),
    array(getGS('NewsImport Settings'), ''),
));

camp_html_display_msgs();
?>
<p></p>

<table border="0" width="600" cellspacing="0" cellpadding="0" class="box_table">
<tr>
  <td valign="top">
    <p><?php putGS('NewsImport provides a way to import news events.'); ?></p>
  </td>
</tr>
</table>

<form name="newsimport_prefs" method="post">
<?php echo SecurityToken::FormParameter(); ?>
<table border="0" width="600" cellspacing="0" cellpadding="0" class="box_table">
<!--
<tr>
  <td><?php putGS('Enter your NewsImport command token'); ?>:</td>
  <td><input type="text" name="f_newsimport_command_token" class="input_text" size="40"
    value="<?php p(SystemPref::Get('Plugin_NewsImport_CommandToken')); ?>" /></td>
</tr>
-->
<tr>
  <td colspan="2" align="center" style="padding-top: 10px;">
    <input type="submit" name="save" value="<?php putGS('Save'); ?>" class="button" />
  </td>
</tr>
</table>
</form>
