<?php
require_once($GLOBALS['g_campsiteDir'].'/db_connect.php');
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/languages.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/LoginAttempts.php');
require_once($GLOBALS['g_campsiteDir'].'/include/captcha/php-captcha.inc.php');
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/lib_campsite.php");
require_once($GLOBALS['g_campsiteDir']."/classes/SystemPref.php");

$defaultLanguage = null;
if (isset($_REQUEST['TOL_Language'])) {
    $defaultLanguage = $_REQUEST['TOL_Language'];
} elseif (isset($_COOKIE['TOL_Language'])) {
    $defaultLanguage = $_COOKIE['TOL_Language'];
}

// Load the language files.
camp_load_translation_strings("globals");
camp_load_translation_strings("home");

$siteTitle = (!empty($Campsite['site']['title'])) ? htmlspecialchars($Campsite['site']['title']) : putGS("Newscoop") . $Campsite['VERSION'];
$email = Input::Get("f_email");
$token = Input::Get("token");
$action = "msg";
if (SystemPref::Get("PasswordRecovery") == 'N') {
    $errors[] = getGS('Password recovery is disabled.');
} elseif (!stristr($email, "@") == false && strlen($token) > 4) {
    $usr = User::FetchUserByEmail($email);
    if ($usr != null) {
        $tokenGenerated = (int) substr($token, -10);
        if ($usr->getPasswordResetToken() == $token
            && (time() - $tokenGenerated < 48 * 3600)) { // valid for 48 hours
            $newPassword = Input::Get("f_password","string");
            if (strlen($newPassword) > 0) {
               $usr->setPassword($newPassword);
               $errors[] = getGS('Your password has been reset.');
            } else {
                $action = "inputs";
            }
        } else {
            $errors[] = getGS('This link is not valid.');
        }
    } else {
        $errors[] = getGS('Bad input parameters.');
    }
} else {
    $errors[] = getGS('Bad input parameters.');
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en" xml:lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title><?php p($siteTitle.' - ').putGS("Password recovery"); ?></title>

  <link rel="shortcut icon" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/images/7773658c3ccbf03954b4dacb029b2229.ico" />
  <link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/admin_stylesheet_new.css" />
  <link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/admin_stylesheet.css" />

  <?php include dirname(__FILE__) . '/javascript_common.php'; ?>
  <script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/crypt.js"></script>
</head>
<body>
  <form name="login_form" method="post" onsubmit="return <?php camp_html_fvalidate(); ?>;">
  <input type="hidden" name="email" value="<?php echo $email; ?>" />
  <input type="hidden" name="token" value="<?php echo $token; ?>" />
  <div class="login_box">
    <div class="logobox"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/sign_big.gif" border="0" alt="" /></div>
    <h2><?php putGS("Password Recovery"); ?></h2>
    <noscript>
    <?php
        putGS('Your browser does not support Javascript or (more likely) you have Javascript disabled. Please fix this to be able to use Newscoop.');
    ?>
    </noscript>

    <?php if (isset($errors)) { ?>
    <div class="login_error">
        <?php foreach ($errors as $error) { ?>
        <p><?php echo $error; ?></p>
        <?php } ?>
        <a class="goto" href="<?php echo $Campsite['WEBSITE_URL']; ?>/admin/login.php"><?php putGS('Go to login'); ?></a>
    </div>
    <?php
    }

    if ($action == 'inputs') {
    ?>
    <table border="0" cellspacing="0" cellpadding="0" class="box_table login" width="420">
    <tr>
      <td align="right">
        <strong><?php putGS("Password"); ?> :</strong>
      </td>
      <td>
        <input type="password" name="f_password" size="32" class="input_text" alt="blank" style="width:250px;" emsg="<?php putGS("Please enter your password."); ?>" />
      </td>
    </tr>
    <tr class="buttonBlock2">
      <td></td>
      <td>
        <input type="submit" class="button" name="Login" value="<?php putGS('Recover password'); ?>" />
      </td>
    </tr>
    </table>

  <?php } ?>
  </div>
</form>
<?php camp_html_copyright_notice(false); ?>
