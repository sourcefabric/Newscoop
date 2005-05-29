<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/configuration.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/lib_campsite.php");
include($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/languages.php");

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
// Load the language files.
//$globalfile = $_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/globals.$defaultLanguage.php";
//if (!is_file($globalfile)) {
//	$defaultLanguage = substr($defaultLanguage, 0, 2);
//	$_REQUEST['TOL_Language'] = $defaultLanguage;
//	$globalfile = $_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/globals.$defaultLanguage.php";
//}
//if (!is_file($globalfile)) {
//	$defaultLanguage = 'en';
//	$_REQUEST['TOL_Language'] = $defaultLanguage;
//	$globalfile = $_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/globals.$defaultLanguage.php";
//}
//$localfile = $_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/locals.$defaultLanguage.php";
//require_once($globalfile);
//require_once($localfile);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
	"http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML>
<HEAD>
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">

	<TITLE><?php  putGS("Login"); ?></TITLE>
</HEAD>
<BODY >

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" >
<TR>
	<TD align="center" style="padding-top: 50px;">
		<img src="/<?php echo $ADMIN; ?>/img/sign_big.gif" border="0">
	</TD>
</tr>
</TABLE>

<CENTER>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input" ALIGN="CENTER" style="margin-top: 20px;">
<FORM NAME="dialog" METHOD="POST" ACTION="do_login.php" >
	<TR>
		<TD COLSPAN="2">
			<B><?php  putGS("Login"); ?></B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2"><?php  putGS('Please enter your user name and password'); ?></TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("User name"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" NAME="UserName" SIZE="32" MAXLENGTH="32" class="input_text">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Password"); ?>:</TD>
		<TD>
		<INPUT TYPE="PASSWORD" NAME="UserPassword" SIZE="32" MAXLENGTH="32" class="input_text">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Language"); ?>:</TD>
		<TD>
		<SELECT name="selectlanguage" class="input_select">
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
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="submit" class="button" NAME="Login" VALUE="<?php  putGS('Login'); ?>">
		</DIV>
		</TD>
	</TR>
</FORM>
</TABLE>
</CENTER>
<?php  if (file_exists("./guest_include.php")) require("./guest_include.php"); ?>
</BODY>
</HTML>