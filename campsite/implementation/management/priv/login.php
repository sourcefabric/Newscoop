<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/configuration.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/lib_campsite.php");
include($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/languages.php");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");

// Delete any cookies they currently have.
setcookie("TOL_UserId", "", time() - 86400);
setcookie("TOL_UserKey", "", time() - 86400);

// Special case for the login screen:
// We have to figure out what language to use.
// If they havent logged in before, we should try to display the
// language as set by the browser.  If the user has logged in before,
// use the language that they previously used.
$defaultLanguage = null;
if (!isset($_REQUEST['TOL_Language'])) {
	// Get the browser languages
	$browserLanguageStr = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
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
selectLanguageFile("/", "globals");
selectLanguageFile("/", "locals");

?>
<head>
	<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">

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

<table border="0" cellspacing="0" cellpadding="6" class="table_input" align="center" style="margin-top: 20px;">
<form name="dialog" method="post" action="do_login.php" >
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
		<td align="right" ><?php putGS("User name"); ?>:</td>
		<td>
		<input type="text" name="UserName" size="32" maxlength="32" class="input_text">
		</td>
	</tr>
	<tr>
		<td align="right" ><?php putGS("Password"); ?>:</td>
		<td>
		<input type="password" name="UserPassword" size="32" maxlength="32" class="input_text">
		</td>
	</tr>
	<tr>
		<td align="right" ><?php putGS("Language"); ?>:</td>
		<td>
		<select name="selectlanguage" class="input_select">
		    <?php 
			foreach($languages as $languageCode => $languageAttrs){
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
	<tr>
		<td colspan="2">
		<div align="center">
		<input type="submit" class="button" name="Login" value="<?php  putGS('Login'); ?>">
		</div>
		</td>
	</tr>
</form>
</table>
<?php  if (file_exists("./guest_include.php")) require("./guest_include.php"); ?>
<div align="center"><p><?php camp_html_copyright_notice(false); ?></p></div>
</body>
