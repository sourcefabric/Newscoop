<?php
require_once($GLOBALS['g_campsiteDir'].'/db_connect.php');
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/languages.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/LoginAttempts.php');
require_once($GLOBALS['g_campsiteDir'].'/include/captcha/php-captcha.inc.php');
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/lib_campsite.php");
require_once($GLOBALS['g_campsiteDir']."/classes/SystemPref.php");

// token
$key = md5(rand(0, (double)microtime()*1000000)).md5(rand(0,1000000));

// Special case for the login screen:
// We have to figure out what language to use.
// If they havent logged in before, we should try to display the
// language as set by the browser.  If the user has logged in before,
// use the language that they previously used.
$defaultLanguage = null;
if (isset($_REQUEST['TOL_Language'])) {
    $defaultLanguage = $_REQUEST['TOL_Language'];
} elseif (isset($_COOKIE['TOL_Language'])) {
    $defaultLanguage = $_COOKIE['TOL_Language'];
} else {
    // Get the browser languages
    $browserLanguageStr = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';
    $browserLanguageArray = preg_split("/[,;]/", $browserLanguageStr);
    $browserLanguagePrefs = array();
    foreach ($browserLanguageArray as $tmpLang) {
        if (!(substr($tmpLang, 0, 2) == 'q=')) {
            $browserLanguagePrefs[] = $tmpLang;
        }
    }
    // Try to match preference exactly.
    foreach ($browserLanguagePrefs as $pref) {
        if (array_key_exists($pref, $languages)) {
            $defaultLanguage = $pref;
            break;
        }
    }
    // Try to match two-letter language code.
    if (is_null($defaultLanguage)) {
        foreach ($browserLanguagePrefs as $pref) {
            if (substr($pref, 0, 2) != "" && array_key_exists(substr($pref, 0, 2), $languages)) {
                $defaultLanguage = $pref;
                break;
            }
        }
    }

    // Default to english if we dont find anything that matches.
    if (is_null($defaultLanguage)) {
        $defaultLanguage = 'en';
    }
    // HACK: the function regGS() strips off the ":en" from
    // english language strings, but only if it knows that
    // the language being displayed is english...and it knows
    // via the cookie.
    $_COOKIE['TOL_Language'] = $defaultLanguage;
    $_REQUEST['TOL_Language'] = $defaultLanguage;
}

// Load the language files.
camp_load_translation_strings("globals");
camp_load_translation_strings("home");

$siteTitle = (!empty($Campsite['site']['title'])) ? htmlspecialchars($Campsite['site']['title']) : putGS("Campsite") . $Campsite['VERSION'];
$email = Input::Get("f_email");
$token = Input::Get("token");
$action = "msg";
if (SystemPref::Get("PasswordRecovery") == 'N') {
    $errors[] = getGS('Password recovery is disabled.') . '<br/> <a href="login.php">' . getGS('login') . '</a>';
} elseif (!stristr($email, "@") == false && strlen($token) > 4) {
    $usr = User::FetchUserByEmail($email);
    if ($usr != null) {
        if ("|" . strtoupper($usr->getPasswordResetToken()) == $token) {
            $newPassword = Input::Get("f_password","string");
            if (strlen($newPassword) > 0) {
               $usr->setPassword($newPassword);
               $errors[] = getGS('Your password has been reset <br/> you may now proceed to')
                   . ' <a href="login.php">' . getGS('login') . '</a>';
            } else {
                $action = "inputs";
            }
        } else {
            $errors[] = getGS('This link is not valid.') . '<br/> <a href="login.php">'
                . getGS('login') . '</a>';
        }
    } else {
        $errors[] = getGS('Bad input parameters.') . '<br/> <a href="login.php">' . getGS('login') .'</a>';
    }
} else {
    $errors[] = getGS('Bad input parameters.') . '<br/> <a href="login.php">' . getGS('login') . '</a>';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en" xml:lang="en">
<head>
  <title><?php p($siteTitle.' - ').putGS("Password recovery"); ?></title>
  <script src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/crypt.js" type="text/javascript"></script>
  <link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/admin_stylesheet.css" />
  <?php include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php"); ?>
</head>
<body>
  <form name="login_form" method="post" onsubmit="return <?php camp_html_fvalidate(); ?>;">
  <input type="hidden" name="email" value="<?php echo $email; ?>" />
  <input type="hidden" name="token" value="<?php echo $token; ?>" />
  <div class="login_box">
    <div class="logobox"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/campsite_logo_big.png" border="0" alt="" /></div>
    <h2><?php putGS("Password Recovery"); ?></h2>
    <noscript>
    <?php
        putGS('Your browser does not support Javascript or (more likely) you have Javascript disabled. Please fix this to be able to use Campsite.');
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
  </div>
  <input type="hidden" name="f_xkoery" value="<?php p($key); ?>" />
  <?php } ?>
</form>
<?php camp_html_copyright_notice(false); ?>