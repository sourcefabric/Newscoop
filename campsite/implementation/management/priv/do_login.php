<?php
camp_load_translation_strings('home');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/User.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/LoginAttempts.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/include/captcha/php-captcha.inc.php');

$f_user_name = Input::Get('f_user_name');
$f_password = Input::Get('f_password');
$f_login_language = Input::Get('f_login_language', 'string', 'en');
$f_is_encrypted = Input::Get('f_is_encrypted', 'int', '1');
$f_captcha_code = Input::Get('f_captcha_code', 'string', '', true);

if (!Input::isValid()) {
	header("Location: /$ADMIN/login.php?error_code=userpass");
	exit;
}

function camp_successful_login($user, $f_login_language)
{
	global $ADMIN;
	$user->initLoginKey();
	LoginAttempts::ClearLoginAttemptsForIp();
	setcookie("LoginUserId", $user->getUserId());
	setcookie("LoginUserKey", $user->getKeyId());
	setcookie("TOL_Language", $f_login_language);
    Article::UnlockByUser($user->getUserId());
	header("Location: /$ADMIN/index.php");
	exit;
}

//
// Valid logins
//
// if user valid, password valid, encrypted, no CAPTCHA -> login
// if user valid, password valid, encrypted, CAPTCHA valid -> login
// if user valid, password valid, not encrypted, no CAPTCHA -> login, upgrade
// if user valid, password valid, not encrypted, CAPTCHA valid -> login, upgrade

//
// Invalid logins
//
// CAPTCHA invalid -> captcha
// If user not valid -> userpass
// password invalid, encrypted -> upgrade
// password invalid, not encrypted -> userpass


$user = User::FetchUserByName($f_user_name, true);
$validateCaptcha = LoginAttempts::MaxLoginAttemptsExceeded();

//
// Valid login section
//
if (!is_null($user)) {
	if ($f_is_encrypted) {
		if ($user->isValidPassword($f_password, true)) {
			if (!$validateCaptcha || PhpCaptcha::Validate($f_captcha_code, true)) {
				// if user valid, password valid, encrypted, no CAPTCHA -> login
				// if user valid, password valid, encrypted, CAPTCHA valid -> login
				camp_successful_login($user, $f_login_language);
			}
		}
	} else {
		// not encrypted
		if ($user->isValidOldPassword($f_password)) {
			if (!$validateCaptcha || PhpCaptcha::Validate($f_captcha_code, true)) {
				// if user valid, password valid, not encrypted, CAPTCHA valid -> upgrade, login
				// if user valid, password valid, not encrypted, no CAPTCHA -> upgrade, login
				$user->setPassword($f_password);
				camp_successful_login($user, $f_login_language);
			}
		}
	}
}

//
// Invalid logins start here.
//

// Record the attempt
LoginAttempts::RecordLoginAttempt();

// CAPTCHA invalid -> captcha login page
if ($validateCaptcha && !PhpCaptcha::Validate($f_captcha_code, true)) {
	header("Location: /$ADMIN/login.php?error_code=captcha");
	exit;
}

// user valid, password invalid, encrypted, CAPTCHA valid -> upgrade
if (!is_null($user) && $f_is_encrypted && (strlen($user->getPassword()) < 40)) {
	header("Location: /$ADMIN/login.php?error_code=upgrade&f_user_name=$f_user_name");
	exit;
}

// Everything else
header("Location: /$ADMIN/login.php?error_code=userpass");
exit;
?>