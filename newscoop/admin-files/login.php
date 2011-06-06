<?php

require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/languages.php");
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/lib_campsite.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/LoginAttempts.php');
require_once($GLOBALS['g_campsiteDir'].'/include/captcha/php-captcha.inc.php');
require_once($GLOBALS['g_campsiteDir']."/classes/SystemPref.php");
require_once($GLOBALS['g_campsiteDir']."/template_engine/classes/CampRequest.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/User.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/include/crypto/rc4Encrypt.php');
require_once('PEAR.php');

camp_load_translation_strings('home');
camp_load_translation_strings('api');

$error_code = '';
if ($this->getRequest()->isPost() && !empty($_POST['f_user_name']) && !empty($_POST['f_password'])) { // handle login
    $error_code = require_once dirname(__FILE__) . '/do_login.php';
    if (empty($error_code)) { // logged in
        return;
    }
}

// at some situations (e.g. after session expired) we require to have user name/password
// and by this, it is forced for all (ajax) windows where something was put to be saved
$f_force_login = Input::Get('f_force_login');

$auth = Zend_Auth::getInstance();
if ($auth->hasIdentity() && (!$f_force_login)) { // logged in allready
    $this->_helper->redirector('index', 'index');
}

// token
$key = md5(rand(0, (double)microtime()*1000000)).md5(rand(0,1000000));
camp_session_set('xorkey', $key);

// This can be "userpass", "captcha", "upgrade"
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

$siteTitle = (!empty($Campsite['site']['title'])) ? htmlspecialchars($Campsite['site']['title']) : putGS("Newscoop") . $Campsite['VERSION'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en" xml:lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title><?php p($siteTitle.' - ').putGS("Login"); ?></title>

  <link rel="shortcut icon" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/images/7773658c3ccbf03954b4dacb029b2229.ico" />
  <link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/admin_stylesheet_new.css" />
  <link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/admin_stylesheet.css" />

  <?php include dirname(__FILE__) . '/javascript_common.php'; ?>
  <script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/crypt.js"></script>
</head>
<body>

<?php
if (file_exists($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/demo_login.php")) {
    require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/demo_login.php");
}
?>
<form name="login_form" action="" method="post" onsubmit="return <?php camp_html_fvalidate(); ?>;">

<?php if (!empty($_POST['_next'])) {
    // print hidden field function
    $view = $this->view;
    $ignored = array(
        'f_is_encrypted',
        'f_user_name',
        'f_password',
        'f_login_language',
        'f_captcha_code',
        'Login',
        'f_xorkey',
    );

    $printHidden = function($name, $value) use ($view) {
        echo '<input type="hidden" name="', $name, '" value="', $view->escape($value), '" />';
    };

    if (!empty($_POST)) {
        foreach ($_POST as $name => $value) {
            if (in_array($name, $ignored)) {
                continue;
            }

			if (is_array($value)) {
				foreach ($value as $arrayValue) {
					$printHidden("{$name}[]", $arrayValue);
				} 
			} else {
				$printHidden($name, $value);
			}
		}
	}
} ?>

<?php if ($error_code == "upgrade") { ?>
<input type="hidden" name="f_is_encrypted" value="0" />
<?php } else { ?>
<input type="hidden" name="f_is_encrypted" value="1" />
<?php } ?>
<div class="login_box">
<div class="logobox"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/sign_big.gif" border="0" alt="" /></div>
<h2><?php putGS("Login"); ?></h2>
    <noscript>
    <?php
    putGS('Your browser does not support Javascript or (more likely) you have Javascript disabled. Please fix this to be able to use Newscoop.');
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
            putGS("Newscoop has upgraded its security measures.  In order to upgrade your account to use this increased security, you must enter your password again.");
        } elseif ($error_code == 'xorkey') {
            putGS("An error occured in session management. Please reload the login page.");
        }
    ?>
    </div>
    <?php
    }
    ?>

<table border="0" cellspacing="0" cellpadding="0" class="box_table login" width="420">
<?php if (!empty($_POST['_next'])) { ?>
<tr>
    <td colspan="2"><strong class="light">
        <?php
        if ($_GET['request'] == 'ajax' || $requestIsPost) {
            putGS('Your changes will be saved after login.');
            echo '<br />';
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
    <?php putGS("User name"); ?>
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
    <a href="<?php echo $Campsite['WEBSITE_URL']; ?>/admin/password_recovery.php"><?php putGS('Forgot your password?'); ?></a>
  <?php } ?>
  </td>
  <td>
    <noscript>
      <input type="submit" class="button" name="Login" value="<?php  putGS('Login'); ?>" disabled />
    </noscript>
    <script type="text/javascript">
        document.write('<input type="submit" class="button" name="Login" value="<?php putGS('Login'); ?>" <?php if ($error_code != "upgrade") { ?> onclick="if (f_password.value.trim() != \'\' && (f_password.value.trim().length) != 0) f_password.value = rc4encrypt(f_xorkey.value,f_password.value);" <?php } ?>/>');
    </script>
  </td>

</tr>
</table>
</div>
<input type="hidden" name="f_xorkey" value="<?php echo $this->view->escape($key); ?>" />
</form>
<script type="text/javascript">
<?php if ($error_code != "upgrade") { ?>
document.forms.login_form.f_user_name.focus();
<?php } else { ?>
document.forms.login_form.f_password.focus();
<?php } ?>
</script>
<?php camp_html_copyright_notice(false); ?>
</body>
</html>
