<p></p>
<?php 
$translator = \Zend_Registry::get('container')->getService('translator');
$preferencesService = \Zend_Registry::get('container')->getService('system_preferences_service');
?>
<table border="0" width="600" cellspacing="0" cellpadding="0" class="box_table">
<tr>
  <td valign="center">
    <p><?php echo $translator->trans('To get preferences you need:', array(), 'plugin_soundcloud'); ?></p>
  </td>
  <td valign="top">
    <p><?php echo $translator->trans('1. Register on http://soundcloud.com<br>2. Create new application: http://soundcloud.com/you/apps', array(), 'plugin_soundcloud'); ?></p>
  </td>
</tr>
</table>

<form name="recaptcha_prefs" method="post">
<?php echo SecurityToken::FormParameter(); ?>
<table border="0" width="600" cellspacing="0" cellpadding="0" class="box_table">
<tr>
  <td><?php echo $translator->trans('Enter Client ID', array(), 'plugin_soundcloud'); ?>:</td>
  <td><input type="text" name="f_soundcloud_client_id" class="input_text" size="50"
    value="<?php p($preferencesService->get('PLUGIN_SOUNDCLOUD_CLIENT_ID')); ?>" /></td>
</tr>
<tr>
  <td><?php echo $translator->trans('Enter Client secret', array(), 'plugin_soundcloud'); ?>:</td>
  <td><input type="text" name="f_soundcloud_client_secret" class="input_text" size="50"
    value="<?php p($preferencesService->get('PLUGIN_SOUNDCLOUD_CLIENT_SECRET')); ?>" /></td>
</tr>
<tr>
  <td><?php echo $translator->trans('Enter permalink or email address', array(), 'plugin_soundcloud'); ?>:</td>
  <td><input type="text" name="f_soundcloud_username" class="input_text" size="50"
    value="<?php p($preferencesService->get('PLUGIN_SOUNDCLOUD_USERNAME')); ?>" /></td>
</tr>
<tr>
  <td><?php echo $translator->trans('Enter password', array(), 'plugin_soundcloud'); ?>:</td>
  <td><input type="text" name="f_soundcloud_password" class="input_text" size="50"
    value="<?php p($preferencesService->get('PLUGIN_SOUNDCLOUD_PASSWORD')); ?>" /></td>
</tr>
<tr>
  <td colspan="2" align="center" style="padding-top: 10px;">
    <input type="submit" name="save" value="<?php echo $translator->trans('Save'); ?>" class="button" />
    <input type="submit" name="check" value="<?php echo $translator->trans('Check connection', array(), 'plugin_soundcloud'); ?>" class="button" />
  </td>
</tr>
</table>
</form>
