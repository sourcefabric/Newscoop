<?php

require_once($GLOBALS['g_campsiteDir'].'/db_connect.php');
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/languages.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/LoginAttempts.php');
require_once($GLOBALS['g_campsiteDir'].'/include/captcha/php-captcha.inc.php');
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/lib_campsite.php");
require_once($GLOBALS['g_campsiteDir']."/classes/SystemPref.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Log.php");

/**
 * Send token via email
 *
 * @param string $p_email
 * @param string $p_token
 * @return void
 */

$translator = \Zend_Registry::get('container')->getService('translator');

function send_token($p_email, $p_token)
{
    global $Campsite;

    // reset link
    $link = sprintf('%s/admin/password_check_token.php?token=%s&f_email=%s',
        $Campsite['WEBSITE_URL'],
        $p_token,
        $p_email);

    // email message
    $message = $translator->trans("Hi, \n\nfor password recovery, please follow this link: $1", array('$1' => $link), 'home');

    // get from email
    $from = SystemPref::Get('PasswordRecoveryFrom');
    if (empty($from)) {
        $from = 'no-reply@' . $_SERVER['SERVER_NAME'];
    }

    // set headers
    $headers = array(
        'MIME-Version: 1.0',
        'Content-type: text/plain; charset=UTF-8',
        "From: $from",
    );

    // send mail
    mail($p_email,
        '=?UTF-8?B?' . base64_encode($translator->trans('Password recovery', array(), 'home')) . '?=',
        trim(html_entity_decode(strip_tags($message), ENT_QUOTES, 'UTF-8')),
        implode("\r\n", $headers));
}

$defaultLanguage = null;
if (isset($_REQUEST['TOL_Language'])) {
    $defaultLanguage = $_REQUEST['TOL_Language'];
} elseif (isset($_COOKIE['TOL_Language'])) {
    $defaultLanguage = $_COOKIE['TOL_Language'];
}

// Load the language files.
camp_load_translation_strings("globals");
camp_load_translation_strings("home");
$sent = false;
$siteTitle = (!empty($Campsite['site']['title'])) ? htmlspecialchars($Campsite['site']['title']) : $translator->trans("Newscoop", array(), 'home') . $Campsite['VERSION'];
$disabled=false;
if (SystemPref::Get("PasswordRecovery")=='N') {
    $disabled = true;
} else
if (Input::Get("f_post_sent", "int",0)==1) {
    $email = Input::Get("f_email");
    if (stristr($email, "@") == false) { // || stristr($email, ".")==false)
        $errors[] = $translator->trans("Email: incorrect format.", array(), 'home');
    }

    if (!isset($errors)) {
        $usr = User::FetchUserByEmail($email);
        if ($usr!=null && is_numeric($usr->getUserId()) && $usr->getUserId()>0) {
            $usr->setPasswordResetToken();
            $token = $usr->getPasswordResetToken();

            send_token($email, $token);
            $sent = true;
        }
        else {
            $errors[] = $translator->trans("No user is registered with this email.", array(), 'home');
        }
    }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en" xml:lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title><?php p($siteTitle.' - ').$translator->trans("Password Recovery", array(), 'home'); ?></title>

  <link rel="shortcut icon" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/images/7773658c3ccbf03954b4dacb029b2229.ico" />
  <link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/admin_stylesheet_new.css" />
  <link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/admin_stylesheet.css" />

  <?php include dirname(__FILE__) . '/javascript_common.php'; ?>
  <script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/crypt.js"></script>
</head>
<body>
  <form name="login_form" method="post" onsubmit="return <?php camp_html_fvalidate(); ?>;">
  <input type="hidden" name="f_post_sent" value="1" />
  <div class="login_box">
    <div class="logobox"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/sign_big.gif" border="0" alt="" /></div>
    <h2><?php echo $translator->trans("Password Recovery", array(), 'home'); ?></h2>
    <noscript>
    <?php
    echo $translator->trans('Your browser does not support Javascript or (more likely) you have Javascript disabled. Please fix this to be able to use Newscoop.', array(), 'home');
    ?>
    </noscript>
    <?php
    if (isset($errors)) {
    ?>
    <div class="login_error">
        <?php
        foreach ($errors as $error) {
            echo "$error <br/>";
        }
        ?>
    </div>
    <?php
    }
    if (!$sent && !$disabled) {
    ?>
    <table border="0" cellspacing="0" cellpadding="0" class="box_table login" width="420">
    <tr>
      <td align="right">
        <strong><?php echo $translator->trans("Email"); ?>:</strong>
      </td>
      <td>
        <input type="text" name="f_email" size="100" class="input_text" alt="blank" style="width:250px;" emsg="<?php echo $translator->trans("Please enter your email.", array(), 'home'); ?>" />
      </td>
    </tr>
    <tr class="buttonBlock2">
    <td><a href="<?php echo $Campsite['WEBSITE_URL']; ?>/admin/login"><?php echo $translator->trans('Back to login.', array(), 'home'); ?></a></td>
      <td>
        <input type="submit" class="button" name="Login" value="<?php  echo $translator->trans('Recover password', array(), 'home'); ?>" />
      </td>
    </tr>
    </table>

    <?php } else if ($disabled) { ?>
    <p><?php echo $translator->trans('Password recovery is disabled.', array(), 'home'); ?></p>
    <a class="goto" href="<?php echo $Campsite['WEBSITE_URL']; ?>/admin/login"><?php echo $translator->trans('Back to login', array(), 'home'); ?></a>
    <?php } else { ?>
    <p><?php echo $translator->trans('An email with instructions on how to recover you password has been sent to your inbox.', array(), 'home'); ?></p>
    <a class="goto" href="<?php echo $Campsite['WEBSITE_URL']; ?>/admin/login"><?php echo $translator->trans('Proceed to login.', array(), 'home'); ?></a>
    <?php } ?>
    </div>
  </div>
  </form>

<?php camp_html_copyright_notice(false); ?>
