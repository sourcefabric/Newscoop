<?php
require_once($GLOBALS['g_campsiteDir'].'/db_connect.php');
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/languages.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/LoginAttempts.php');
require_once($GLOBALS['g_campsiteDir'].'/include/captcha/php-captcha.inc.php');
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/lib_campsite.php");
require_once($GLOBALS['g_campsiteDir']."/classes/SystemPref.php");

// Get request.
$requestId = Input::Get('request', 'string', '', TRUE);
$request = camp_session_get("request_$requestId", '');
$requestIsPost = FALSE;
if (!empty($request)) {
    $tmp = unserialize($request);
    $requestIsPost = !empty($tmp['post']);
    unset($tmp);
}

// Fix for CS-2276
$LiveUser->logout();
// Delete the cookies
setcookie("LoginUserId", "", time() - 86400);
setcookie("LoginUserKey", "", time() - 86400);

// token
$key = md5(rand(0, (double)microtime()*1000000)).md5(rand(0,1000000));
camp_session_set('xorkey', $key);
// Delete any cookies they currently have.
setcookie("LoginUserId", "", time() - 86400);
setcookie("LoginUserKey", "", time() - 86400);

// This can be "userpass", "captcha", "upgrade"
$error_code = isset($_REQUEST['error_code']) ? $_REQUEST['error_code'] : '';
$f_user_name = isset($_REQUEST['f_user_name']) ? $_REQUEST['f_user_name'] : '';

LoginAttempts::DeleteOldLoginAttempts();

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

// Store request again.
camp_session_set("request_$requestId", $request);

// Load the language files.
camp_load_translation_strings("globals");
camp_load_translation_strings("home");

$siteTitle = (!empty($Campsite['site']['title'])) ? htmlspecialchars($Campsite['site']['title']) : putGS("Campsite") . $Campsite['VERSION'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en" xml:lang="en">
<head>
  <script src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/crypt.js" type="text/javascript"></script>
  <link rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">
  <?php include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php"); ?>
  <title><?php p($siteTitle.' - ').putGS("Login"); ?></title>
</head>
<body>

<?php
if (file_exists($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/demo_login.php")) {
    require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/demo_login.php");
}
?>
<form name="login_form" method="post" action="do_login.php?request=<?php echo $requestId; ?>" onsubmit="return <?php camp_html_fvalidate(); ?>;">
<?php if ($error_code == "upgrade") { ?>
<input type="hidden" name="f_is_encrypted" value="0" />
<?php } else { ?>
<input type="hidden" name="f_is_encrypted" value="1" />
<?php } ?>
<div class="login_box">
<div class="logobox"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/campsite_logo_big.png" border="0" alt="" /></div>
<h2><?php putGS("Login"); ?></h2>
    <noscript>
    <?php
    putGS('Your browser does not support Javascript or (more likely) you have Javascript disabled. Please fix this to be able to use Campsite.');
    ?>
    </noscript>
    <?php
    if (strlen($error_code) > 0) {
    ?>
    <div class="login_error">
    <?php
        if ($error_code == "userpass") {
            putGS("Login failed");
            echo "<br><br>";
            putGS('Please make sure that you typed the correct user name and password.');
            //putGS('If your problem persists please contact the site administrator $1','');
        } elseif ($error_code == "captcha") {
            putGS('CAPTCHA code is not valid.  Please try again.');
        } elseif ($error_code == "upgrade") {
            putGS("Campsite has upgraded its security measures.  In order to upgrade your account to use this increased security, you must enter your password again.");
        } elseif ($error_code == 'xorkey') {
            putGS("An error occured in session management. Please reload the login page.");
        }
    ?>
    </div>
    <?php
    }
    ?>

<table border="0" cellspacing="0" cellpadding="0" class="box_table login" width="420">
<?php if (!empty($_GET['request'])) { ?>
<tr>
    <td colspan="2"><strong class="light">
        <?php
        if ($_GET['request'] == 'ajax' || $requestIsPost) {
            putGS('Your work has been saved.');
        }
        putGS('Please login to continue.');
        ?>
    </strong></td>
</tr>
<?php } ?>
<tr>
  <td colspan="2"><span class="light"><?php putGS('Please enter your user name and password'); ?></span></td>
</tr>
<tr>
  <td align="right" ><strong>
    <?php putGS("Account name"); ?>
    :</strong></td>
  <td>
    <?php if ($error_code != "upgrade") { ?>
    <input type="text" name="f_user_name" size="32" class="input_text" alt="blank" style="width:250px;" emsg="<?php putGS("Please enter your user name."); ?>" />
    <?php } else { ?>
    <input type="hidden" name="f_user_name" value="<?php p($f_user_name); ?>" />
    <?php echo p(htmlspecialchars($f_user_name)); ?>
    <?php } ?>
  </td>
</tr>
<tr>
  <td align="right"><strong>
    <?php putGS("Password"); ?>
    :</strong></td>
  <td>
    <input type="password" name="f_password" size="32" class="input_text" alt="blank" style="width:250px;" emsg="<?php putGS("Please enter your password."); ?>" />
  </td>
</tr>
<tr>
  <td align="right"><strong>
    <?php putGS("Language"); ?>
    :</strong></td>
  <td>
    <select name="f_login_language" class="input_select" style="width:253px;">
    <?php
        foreach ($languages as $languageCode => $languageAttrs) {
            $languageName = isset($languageAttrs['orig_name']) ?
                $languageAttrs['orig_name'] : $languageAttrs['name'];
            $languageName = htmlspecialchars($languageName);
            print "<option value=\"$languageCode\"";
            if ($languageCode == $defaultLanguage) {
                print " selected ";
            }
            print ">$languageName</option>";
        }
        unset($languageCode);
        unset($languageAttrs);
        unset($languageName);
    ?>
    </select>
  </td>
</tr>
<!-- CAPTCHA-->
<?php if (LoginAttempts::MaxLoginAttemptsExceeded()) { ?>
<tr>
  <td colspan="2" align="center">
    <img src="<?php echo $Campsite['WEBSITE_URL']; ?>/include/captcha/image.php" />
  </td>
</tr>
<tr>
  <td colspan="2" align="center">
    <?php  putGS('Type the code shown above:'); ?>
    <input name="f_captcha_code" size="5" class="input_text" alt="blank" emsg="<?php putGS("Please enter the code shown in the image."); ?>" />
  </td>
</tr>
<?php } ?>
<!-- CAPTCHA-->
<tr class="buttonBlock2">
  <td>
  <?php if (SystemPref::Get("PasswordRecovery") == 'Y') { ?>
    <a href="password_recovery.php"><?php putGS('Forgot your password?'); ?></a>
  <?php } ?>
  </td>
  <td>
    <noscript>
      <input type="submit" class="button" name="Login" value="<?php  putGS('Login'); ?>" disabled />
    </noscript>
    <script type="text/javascript" language="JavaScript">
        document.write('<input type="submit" class="button" name="Login" value="<?php putGS('Login'); ?>" <?php if ($error_code != "upgrade") { ?> onclick="if (f_password.value.trim() != \'\' && (f_password.value.trim().length) != 0) f_password.value = rc4encrypt(f_xkoery.value,f_password.value);" <?php } ?>/>');
    </script>
  </td>

</tr>
</table>
</div>
<input type="hidden" name="f_xkoery" value="<?php p($key); ?>" />
</form>
<script type="text/javascript">
<?php if ($error_code != "upgrade") { ?>
document.forms.login_form.f_user_name.focus();
<?php } else { ?>
document.forms.login_form.f_password.focus();
<?php } ?>
</script>
<?php camp_html_copyright_notice(false); ?>
