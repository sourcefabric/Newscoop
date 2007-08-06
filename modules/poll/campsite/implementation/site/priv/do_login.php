<?php
camp_load_translation_strings('home');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/XR_CcClient.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/User.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/LoginAttempts.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/SystemPref.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/include/captcha/php-captcha.inc.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/include/crypto/rc4Encrypt.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/include/pear/PEAR.php');
camp_load_translation_strings("api");

$f_user_name = Input::Get('f_user_name');
$f_password = Input::Get('f_password');
$f_login_language = Input::Get('f_login_language', 'string', 'en');
$f_is_encrypted = Input::Get('f_is_encrypted', 'int', '1');
$f_captcha_code = Input::Get('f_captcha_code', 'string', '', true);

$xorkey = camp_session_get('xorkey', '');
if (trim($xorkey) == '') {
    camp_html_goto_page("/$ADMIN/login.php?error_code=xorkey");
}
$t_password = camp_passwd_decrypt($xorkey, $f_password);
$f_password = sha1($t_password);


if (!Input::isValid()) {
    camp_html_goto_page("/$ADMIN/login.php?error_code=userpass");
}

function camp_successful_login($user, $f_login_language)
{
    global $ADMIN, $LiveUser, $LiveUserAdmin;

    $user->initLoginKey();
    $data = array('KeyId' => $user->getKeyId());
    $permUserId = $LiveUser->_perm->getProperty('perm_user_id');
    $LiveUserAdmin->updateUser($data, $permUserId);
    $LiveUser->updateProperty(true, true);
    LoginAttempts::ClearLoginAttemptsForIp();
    setcookie("LoginUserId", $user->getUserId());
    setcookie("LoginUserKey", $user->getKeyId());
    setcookie("TOL_Language", $f_login_language);
    Article::UnlockByUser($user->getUserId());
    camp_html_goto_page("/$ADMIN/index.php");
}

function camp_passwd_decrypt($xorkey, $password)
{
    return rc4($xorkey, base64ToText($password));
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

if (!$LiveUser->isLoggedIn() ||
        ($f_user_name && $LiveUser->getProperty('handle') != $f_user_name)) {
    if (!$f_user_name) {
        $LiveUser->login(null, null, true);
    } else {
        if (!$LiveUser->login($f_user_name, $t_password, false)) {
            camp_html_goto_page("/$ADMIN/login.php?error_code=userpass");
        }
    }
}

$user = User::FetchUserByName($f_user_name, true);
$validateCaptcha = LoginAttempts::MaxLoginAttemptsExceeded();

//
// Valid login section
//
if ($LiveUser->isLoggedIn()) {
    if (is_null($user)) {
        // load data to User from liveuser_users
        // and create the user object
    }
    if (!$validateCaptcha || PhpCaptcha::Validate($f_captcha_code, true)) {
        // if user valid, password valid, encrypted, no CAPTCHA -> login
        // if user valid, password valid, encrypted, CAPTCHA valid -> login
        if (SystemPref::Get("UseCampcasterAudioclips") == 'Y') {
            $ccLogin = camp_campcaster_login($f_user_name, $t_password);
            if (PEAR::isError($ccLogin)) {
                if ($ccLogin->getCode() == '802') {
                    camp_html_add_msg(getGS("Your user is not a valid Campcaster user"));
                } else {
                    camp_html_add_msg(getGS("There was an error logging in to the Campcaster server"));
                }
            }
        }
        camp_successful_login($user, $f_login_language);
    }
}

//
// Invalid logins start here.
//

// Record the attempt
LoginAttempts::RecordLoginAttempt();

// CAPTCHA invalid -> captcha login page
if ($validateCaptcha && !PhpCaptcha::Validate($f_captcha_code, true)) {
    camp_html_goto_page("/$ADMIN/login.php?error_code=captcha");
}

// user valid, password invalid, encrypted, CAPTCHA valid -> upgrade
if (!is_null($user) && $f_is_encrypted && (strlen($user->getPassword()) < 40)) {
    camp_html_goto_page("/$ADMIN/login.php?error_code=upgrade&f_user_name=$f_user_name");
}

// Everything else
camp_html_goto_page("/$ADMIN/login.php?error_code=userpass");
?>