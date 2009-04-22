<?php
camp_load_translation_strings('home');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/User.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/include/captcha/php-captcha.inc.php');


$f_user_name = Input::Get('f_user_name');
$f_user_email = Input::Get('f_user_email');
$f_captcha_code = Input::Get('f_captcha_code', 'string', '', true);


// CAPTCHA invalid -> captcha login page
if (!PhpCaptcha::Validate($f_captcha_code, true)) {
    camp_html_goto_page("/$ADMIN/password_restore.php?error_code=captcha");
}

?>