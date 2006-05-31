<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/configuration.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/lib_campsite.php");
include($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/languages.php");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/LoginAttempts.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/include/captcha/php-captcha.inc.php');

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
if (!isset($_REQUEST['TOL_Language'])) {
	// Get the browser languages
	$browserLanguageStr = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';
	$browserLanguageArray = split("[,;]", $browserLanguageStr);
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
	$_REQUEST['TOL_Language'] = $defaultLanguage;
}
else {
	$defaultLanguage = $_REQUEST['TOL_Language'];
}

// Load the language files.
camp_load_language("globals");
camp_load_language("home");

?>
<head>
	<script src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/sha1.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.config.js"></script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.core.js"></script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.lang-enUS.js"></script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.validators.js"></script>
	<TITLE><?php  putGS("Login"); ?></title>
</head>
<body >

<table border="0" cellspacing="0" cellpadding="1" width="100%" >
<tr>
	<td align="center" style="padding-top: 50px;">
		<img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/sign_big.gif" border="0">
	</td>
</tr>
</table>

<table width="400px" border="0" cellspacing="0" cellpadding="6" align="center" style="margin-top: 20px; background-color: #d5e2ee;	border: 1px solid #8baed1;">
<form method="post" action="do_login.php" onsubmit="return validateForm(this, 0, 1, 0, 1, 8);">
<?php if ($error_code == "upgrade") { ?>
<input type="hidden" name="f_is_encrypted" value="0">
<?php } else { ?>
<input type="hidden" name="f_is_encrypted" value="1">
<?php } ?>

	<tr>
		<td colspan="2" align="center">
			<span style="color:#FF0000;">
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
				}
				?>
			</span>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<b><?php  putGS("Login"); ?></b>
			<hr noshade size="1" color="black">
		</td>
	</tr>

	<tr>
		<td colspan="2"><?php putGS('Please enter your user name and password'); ?></td>
	</tr>
	<tr>
		<td align="right" ><?php putGS("Account name"); ?>:</td>
		<td>
		<?php if ($error_code != "upgrade") { ?>
		<input type="text" name="f_user_name" size="32" class="input_text" alt="blank" emsg="<?php putGS("Please enter your user name."); ?>">
		<?php } else { ?>
		<input type="hidden" name="f_user_name" value="<?php p($f_user_name); ?>">
		<?php echo p(htmlspecialchars($f_user_name)); ?>
		<?php } ?>
		</td>
	</tr>
	<tr>
		<td align="right" ><?php putGS("Password"); ?>:</td>
		<td>
		<input type="password" name="f_password" size="32" class="input_text" alt="blank" emsg="<?php putGS("Please enter your password."); ?>">
		</td>
	</tr>
	<tr>
		<td align="right" ><?php putGS("Language"); ?>:</td>
		<td>
		<select name="f_login_language" class="input_select">
		    <?php
			foreach ($languages as $languageCode => $languageAttrs) {
			    $languageName = isset($languageAttrs['orig_name'])?
			    	$languageAttrs['orig_name']:$languageAttrs['name'];
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
			<img src="<?php echo $Campsite['WEBSITE_URL']; ?>/include/captcha/image.php">
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center">
			<?php  putGS('Type the code shown above:'); ?>
			<input name="f_captcha_code" size="5" class="input_text" alt="blank" emsg="<?php putGS("Please enter the code shown in the image."); ?>">
		</td>
	</tr>
	<?php } ?>
	<!-- CAPTCHA-->

	<tr>
		<td colspan="2">
		<div align="center">
		<input type="submit" class="button" name="Login" value="<?php  putGS('Login'); ?>" <?php if ($error_code != "upgrade") { ?> onclick="if (f_password.value.trim() != '') f_password.value = hex_sha1(f_password.value);" <?php } ?>>
		</div>
		</td>
	</tr>
</form>
</table>
<?php camp_html_copyright_notice(false); ?>
