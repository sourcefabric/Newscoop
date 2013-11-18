<?php
$translator = \Zend_Registry::get('container')->getService('translator');
$preferencesService = \Zend_Registry::get('container')->getService('system_preferences_service');
// User role depend on path to this file.
if (strpos($call_script, '/recaptcha/admin/') !== false && $g_user->hasPermission('plugin_recaptcha_admin')) {
    $is_admin = true;
}

// Check permissions
if (!$is_admin) {
    camp_html_display_error($translator->trans('You do not have the right to manage reCAPTCHA.', array(), 'plugin_recaptcha'));
    exit;
}

if (Input::Get('save')) {
    if (!SecurityToken::isValid()) {
        camp_html_display_error($translator->trans('Invalid security token!'));
        exit;
    }

    $f_recaptcha_enabled = Input::Get('f_recaptcha_enabled', 'string', 'N');
    $f_recaptcha_subscriptions_enabled = Input::Get('f_recaptcha_subscriptions_enabled', 'string', 'N');
    $f_recaptcha_public_key = Input::Get('f_recaptcha_public_key', 'string');
    $f_recaptcha_private_key = Input::Get('f_recaptcha_private_key', 'string');

    $preferencesService->set('PLUGIN_RECAPTCHA_ENABLED', $f_recaptcha_enabled);
    $preferencesService->set('PLUGIN_RECAPTCHA_SUBSCRIPTIONS_ENABLED', $f_recaptcha_subscriptions_enabled);
    $preferencesService->set('PLUGIN_RECAPTCHA_PUBLIC_KEY', $f_recaptcha_public_key);
    $preferencesService->set('PLUGIN_RECAPTCHA_PRIVATE_KEY', $f_recaptcha_private_key);

    camp_html_add_msg($translator->trans('reCAPTCHA preferences updated.', array(), 'plugin_recaptcha'), 'ok');
}

echo camp_html_breadcrumbs(array(
    array($translator->trans('Plugins'), $Campsite['WEBSITE_URL'] . '/admin/plugins/manage.php'),
    array($translator->trans('reCAPTCHA', array(), 'plugin_recaptcha'), ''),
    array($translator->trans('reCAPTCHA Settings', array(), 'plugin_recaptcha'), ''),
));

camp_html_display_msgs();
?>
<p></p>

<table border="0" width="600" cellspacing="0" cellpadding="0" class="box_table">
<tr>
  <td width="190">
    <img src="<?php echo $Campsite['WEBSITE_URL']; ?>/plugins/recaptcha/css/recaptcha-logo.gif" />
  </td>
  <td valign="top">
    <p><?php echo $translator->trans('reCAPTCHA provides a simple way to place a CAPTCHA on your Newscoop website, helping you stop bots from abusing it.', array(), 'plugin_recaptcha'); ?></p>
    <p><?php echo $translator->trans('To use this plugin you need', array(), 'plugin_recaptcha'); ?>:</p>
    <p>- <?php echo $translator->trans('Enable it and input the reCAPTCHA key in the form below. If you do not have yet your key, you can create it', array(), 'plugin_recaptcha'); ?> <a
        href="https://www.google.com/recaptcha/admin/create" target="_blank"><?php echo $translator->trans('here', array(), 'plugin_recaptcha'); ?></a>.</p>
    <p>- <?php echo $translator->trans('Add the {{ recaptcha }} tag to your template file containing the comments/subscription form', array(), 'plugin_recaptcha'); ?>.</p>
  </td>
</tr>
</table>

<form name="recaptcha_prefs" method="post">
<?php echo SecurityToken::FormParameter(); ?>
<table border="0" width="600" cellspacing="0" cellpadding="0" class="box_table">
<tr>
  <td align="left"><?php echo $translator->trans('Enable reCAPTCHA for comments', array(), 'plugin_recaptcha'); ?></td>
  <td><input type="checkbox" name="f_recaptcha_enabled" value="Y" <?php if ($preferencesService->PLUGIN_RECAPTCHA_ENABLED == 'Y') p('checked'); ?> /></td>
</tr>
<tr>
  <td align="left"><?php echo $translator->trans('Enable reCAPTCHA for subscriptions', array(), 'plugin_recaptcha'); ?></td>
  <td><input type="checkbox" name="f_recaptcha_subscriptions_enabled" value="Y" <?php if ($preferencesService->PLUGIN_RECAPTCHA_SUBSCRIPTIONS_ENABLED == 'Y') p('checked'); ?> /></td>
</tr>
<tr>
  <td><?php echo $translator->trans('Enter your reCAPTCHA public key', array(), 'plugin_recaptcha'); ?>:</td>
  <td><input type="text" name="f_recaptcha_public_key" class="input_text" size="40"
    value="<?php p($preferencesService->PLUGIN_RECAPTCHA_PUBLIC_KEY); ?>" /></td>
</tr>
<tr>
  <td><?php echo $translator->trans('Enter your reCAPTCHA private key', array(), 'plugin_recaptcha'); ?>:</td>
  <td><input type="text" name="f_recaptcha_private_key" class="input_text" size="40"
    value="<?php p($preferencesService->PLUGIN_RECAPTCHA_PRIVATE_KEY); ?>" /></td>
</tr>
<tr>
  <td colspan="2" align="center" style="padding-top: 10px;">
    <input type="submit" name="save" value="<?php echo $translator->trans('Save'); ?>" class="button" />
  </td>
</tr>
</table>
</form>
