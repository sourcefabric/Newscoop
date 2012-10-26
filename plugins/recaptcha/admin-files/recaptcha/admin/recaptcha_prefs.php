<?php
camp_load_translation_strings('plugin_recaptcha');

// User role depend on path to this file.
if (strpos($call_script, '/recaptcha/admin/') !== false && $g_user->hasPermission('plugin_recaptcha_admin')) {
    $is_admin = true;
}

// Check permissions
if (!$is_admin) {
    camp_html_display_error(getGS('You do not have the right to manage reCAPTCHA.'));
    exit;
}

if (Input::Get('save')) {
    if (!SecurityToken::isValid()) {
        camp_html_display_error(getGS('Invalid security token!'));
        exit;
    }

    $f_recaptcha_enabled = Input::Get('f_recaptcha_enabled', 'string', 'N');
    $f_recaptcha_subscriptions_enabled = Input::Get('f_recaptcha_subscriptions_enabled', 'string', 'N');
    $f_recaptcha_public_key = Input::Get('f_recaptcha_public_key', 'string');
    $f_recaptcha_private_key = Input::Get('f_recaptcha_private_key', 'string');

    SystemPref::Set('PLUGIN_RECAPTCHA_ENABLED', $f_recaptcha_enabled);
    SystemPref::Set('PLUGIN_RECAPTCHA_SUBSCRIPTIONS_ENABLED', $f_recaptcha_subscriptions_enabled);
    SystemPref::Set('PLUGIN_RECAPTCHA_PUBLIC_KEY', $f_recaptcha_public_key);
    SystemPref::Set('PLUGIN_RECAPTCHA_PRIVATE_KEY', $f_recaptcha_private_key);

    camp_html_add_msg(getGS('reCAPTCHA preferences updated.'), 'ok');
}

echo camp_html_breadcrumbs(array(
    array(getGS('Plugins'), $Campsite['WEBSITE_URL'] . '/admin/plugins/manage.php'),
    array(getGS('reCAPTCHA'), ''),
    array(getGS('reCAPTCHA Settings'), ''),
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
    <p><?php putGS('reCAPTCHA provides a simple way to place a CAPTCHA on your Newscoop website, helping you stop bots from abusing it.'); ?></p>
    <p><?php putGS('To use this plugin you need'); ?>:</p>
    <p>- <?php putGS('Enable it and input the reCAPTCHA key in the form below. If you do not have yet your key, you can create it'); ?> <a
        href="https://www.google.com/recaptcha/admin/create" target="_blank"><?php putGS('here'); ?></a>.</p>
    <p>- <?php putGS('Add the {{ recaptcha }} tag to your template file containing the comments/subscription form'); ?>.</p>
  </td>
</tr>
</table>

<form name="recaptcha_prefs" method="post">
<?php echo SecurityToken::FormParameter(); ?>
<table border="0" width="600" cellspacing="0" cellpadding="0" class="box_table">
<tr>
  <td align="left"><?php putGS('Enable reCAPTCHA for comments'); ?></td>
  <td><input type="checkbox" name="f_recaptcha_enabled" value="Y" <?php if (SystemPref::Get('PLUGIN_RECAPTCHA_ENABLED') == 'Y') p('checked'); ?> /></td>
</tr>
<tr>
  <td align="left"><?php putGS('Enable reCAPTCHA for subscriptions'); ?></td>
  <td><input type="checkbox" name="f_recaptcha_subscriptions_enabled" value="Y" <?php if (SystemPref::Get('PLUGIN_RECAPTCHA_SUBSCRIPTIONS_ENABLED') == 'Y') p('checked'); ?> /></td>
</tr>
<tr>
  <td><?php putGS('Enter your reCAPTCHA public key'); ?>:</td>
  <td><input type="text" name="f_recaptcha_public_key" class="input_text" size="40"
    value="<?php p(SystemPref::Get('PLUGIN_RECAPTCHA_PUBLIC_KEY')); ?>" /></td>
</tr>
<tr>
  <td><?php putGS('Enter your reCAPTCHA private key'); ?>:</td>
  <td><input type="text" name="f_recaptcha_private_key" class="input_text" size="40"
    value="<?php p(SystemPref::Get('PLUGIN_RECAPTCHA_PRIVATE_KEY')); ?>" /></td>
</tr>
<tr>
  <td colspan="2" align="center" style="padding-top: 10px;">
    <input type="submit" name="save" value="<?php putGS('Save'); ?>" class="button" />
  </td>
</tr>
</table>
</form>
