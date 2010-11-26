<?php

function curPageURL() {
    $pageURL = 'http';
    if ($_SERVER["HTTPS"] == "on") {
        $pageURL .= "s";
    }
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"];
    }
    return $pageURL;
}

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
$sent = false;
$siteTitle = (!empty($Campsite['site']['title'])) ? htmlspecialchars($Campsite['site']['title']) : putGS("Campsite") . $Campsite['VERSION'];
$disabled=false;
if (SystemPref::Get("PasswordRecovery")=='N') {
    $disabled = true;
} else
if (Input::Get("f_post_sent", "int",0)==1) {
    $email = Input::Get("f_email");
    if (stristr($email, "@") == false) { // || stristr($email, ".")==false)
        $errors[] = getGS("Email: incorrect format.");
    }

    if (!isset($errors)) {
        $usr = User::FetchUserByEmail($email);
        if ($usr!=null && is_numeric($usr->getUserId()) && $usr->getUserId()>0) {
            $usr->setPasswordResetToken();
            $token = $usr->getPasswordResetToken();
            $subject = getGS("Password recovery email");
            $link = curPageURL() . "/admin/password_check_token.php?";
            $TextMessage = getGS("Hi, \nfor password recovery, please follow this link: %s");
            $HTMLMessage = getGS('Hi, <br>for password recovery, please follow this link: <a href="%s">Recover Password</a>');
            $link = htmlentities($link);
            $TextMessage = sprintf($TextMessage,$link . "token=|" . strtoupper($token) ."&f_email=" . $email);
            $HTMLMessage = sprintf($HTMLMessage,$link . "token=|" . strtoupper($token) ."&f_email=" . $email);
            $boundary1   =rand(0,9)."-"
                .rand(10000000000,9999999999)."-"
                .rand(10000000000,9999999999)."=:"
                .rand(10000,99999);
            $boundary2   =rand(0,9)."-".rand(10000000000,9999999999)."-"
                .rand(10000000000,9999999999)."=:"
                .rand(10000,99999);

            $Body        =<<<AKAM
MIME-Version: 1.0
Content-Type: multipart/alternative;
    boundary="$boundary1"

This is a multi-part message in MIME format.

--$boundary1
Content-Type: text/plain;
    charset="windows-1256"
Content-Transfer-Encoding: quoted-printable

$TextMessage
--$boundary1
Content-Type: text/html;
    charset="windows-1256"
Content-Transfer-Encoding: quoted-printable

$HTMLMessage

--$boundary1--
AKAM;

$Headers     =<<<AKAM
From: Campsite admin
MIME-Version: 1.0
Content-Type: multipart/alternative;
    boundary="$boundary1"
AKAM;
            mail($email, $subject, $Body, $Headers);
            $sent=true;

        }
        else {
            $errors[] = getGS("No user is registered with this email.");
        }
    }

}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en" xml:lang="en">
<head>
  <script src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/crypt.js" type="text/javascript"></script>
  <link rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">
  <?php include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php"); ?>
  <title><?php p($siteTitle.' - ').putGS("Password Recovery"); ?></title>
</head>
<body>
  <form name="login_form" method="post" onsubmit="return <?php camp_html_fvalidate(); ?>;">
  <input type="hidden" name="f_post_sent" value="1" />
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
    if (!$sent && !$disabled) {
    ?>
    <table border="0" cellspacing="0" cellpadding="0" class="box_table login" width="420">
    <tr>
      <td align="right">
        <strong><?php putGS("Email"); ?> :</strong>
      </td>
      <td>
        <input type="text" name="f_email" size="100" class="input_text" alt="blank" style="width:250px;" emsg="<?php putGS("Please enter your email."); ?>" />
      </td>
    </tr>
    <tr class="buttonBlock2">
      <td><a href="login.php">Back to login.</a></td>
      <td>
        <input type="submit" class="button" name="Login" value="<?php  putGS('Recover password'); ?>" />
      </td>
    </tr>
    </table>
    <?php
    } else if ($disabled) {
        putGS('Password recovery is disabled.<br/> <a href="login.php">login</a>');
    } else {
        putGS('An email with instructions on how to recover you password has been sent to your inbox.<br/><a href="login.php">Proceed to login.</a>');
    }
    ?>
  </div>
  <input type="hidden" name="f_xkoery" value="<?php p($key); ?>" />
  </form>
<?php camp_html_copyright_notice(false); ?>
