<p></p>

<table border="0" width="600" cellspacing="0" cellpadding="0" class="box_table">
<tr>
  <td valign="center">
    <p><?php putGS('To get preferences you need:'); ?></p>
  </td>
  <td valign="top">
    <p><?php putGS('1. Register on http://soundcloud.com<br>2. Create new application: http://soundcloud.com/you/apps'); ?></p>
  </td>
</tr>
</table>

<form name="recaptcha_prefs" method="post">
<?php echo SecurityToken::FormParameter(); ?>
<table border="0" width="600" cellspacing="0" cellpadding="0" class="box_table">
<tr>
  <td><?php putGS('Enter Client ID'); ?>:</td>
  <td><input type="text" name="f_soundcloud_client_id" class="input_text" size="50"
    value="<?php p(SystemPref::Get('PLUGIN_SOUNDCLOUD_CLIENT_ID')); ?>" /></td>
</tr>
<tr>
  <td><?php putGS('Enter Client secret'); ?>:</td>
  <td><input type="text" name="f_soundcloud_client_secret" class="input_text" size="50"
    value="<?php p(SystemPref::Get('PLUGIN_SOUNDCLOUD_CLIENT_SECRET')); ?>" /></td>
</tr>
<tr>
  <td><?php putGS('Enter permalink or email address'); ?>:</td>
  <td><input type="text" name="f_soundcloud_username" class="input_text" size="50"
    value="<?php p(SystemPref::Get('PLUGIN_SOUNDCLOUD_USERNAME')); ?>" /></td>
</tr>
<tr>
  <td><?php putGS('Enter password'); ?>:</td>
  <td><input type="text" name="f_soundcloud_password" class="input_text" size="50"
    value="<?php p(SystemPref::Get('PLUGIN_SOUNDCLOUD_PASSWORD')); ?>" /></td>
</tr>
<tr>
  <td colspan="2" align="center" style="padding-top: 10px;">
    <input type="submit" name="save" value="<?php putGS('Save'); ?>" class="button" />
    <input type="submit" name="check" value="<?php putGS('Check connection'); ?>" class="button" />
  </td>
</tr>
</table>
</form>
