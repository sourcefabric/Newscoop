<?php
$auth = Zend_Auth::getInstance();
if ($auth->hasIdentity()) { // ignore for logged user
    return;
}

$f_user_name = Input::Get('f_user_name');
$f_password = Input::Get('f_password');
$f_login_language = Input::Get('f_login_language', 'string', 'en');
$f_is_encrypted = Input::Get('f_is_encrypted', 'int', '1');
$f_captcha_code = Input::Get('f_captcha_code', 'string', '', true);

$xorkey = camp_session_get('xorkey', '');
if (trim($xorkey) == '') {
    return 'xorkey';
}

if (!Input::isValid()) {
    return 'userpass';
}

$t_password = rc4($xorkey, base64ToText($f_password));

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

if (!$auth->hasIdentity()) {
    $repository = $this->_helper->entity->getRepository('Newscoop\Entity\User\Staff');
    $adapter = new Newscoop\Auth\Adapter($repository, $f_user_name, $t_password);
    $result = $auth->authenticate($adapter);

    if ($result->getCode() != Zend_Auth_Result::SUCCESS) {
        return 'userpass';
    }
}

$user = $repository->find($auth->getIdentity());
$validateCaptcha = LoginAttempts::MaxLoginAttemptsExceeded();

// set user for environment
$g_user = $user;
$this->view->user = $user;
Zend_Registry::set('user', $user);

//
// Valid login section
//
if ($auth->hasIdentity()) {
    if (!$validateCaptcha || PhpCaptcha::Validate($f_captcha_code, true)) {
        // if user valid, password valid, encrypted, no CAPTCHA -> login
        // if user valid, password valid, encrypted, CAPTCHA valid -> login

        LoginAttempts::ClearLoginAttemptsForIp();
        setcookie("TOL_Language", $f_login_language);
        Article::UnlockByUser($user->getId());

        // next action GET/POST detection
        if (!empty($_POST['_next']) && $_POST['_next'] == 'get') {
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $method = 'redirect';
        }

        // fix csfr protection
        if (!empty($_POST['csrf'])) {
            $form = new Zend_Form;
            $form->addElement('hash', 'csrf');
            $csrf = $form->getElement('csrf');
            $_POST['csrf'] = $csrf->getHash();
        }

        // fix legacy cs
        if (!empty($_POST[SecurityToken::SECURITY_TOKEN])) {
            $_POST[SecurityToken::SECURITY_TOKEN] = SecurityToken::GetToken();
        }

        // fix uri
        $_SERVER['REQUEST_URI'] = $this->getRequest()->getRequestUri();

        // reset view
        $this->view->legacy = NULL;
        $this->_helper->layout->enableLayout();

        // redirect/forward
        if (!empty($_POST['_next']) && $_POST['_next'] == 'post') { // forward POST request
            $this->_forward($this->_getParam('action'), $this->_getParam('controller'), 'admin');
        } else { // redirect GET request
            $this->_redirect($_SERVER['REQUEST_URI'], array(
                'prependBase' => false,
            ));
        }

        return;
    }
}

//
// Invalid logins start here.
//

// Record the attempt
LoginAttempts::RecordLoginAttempt();

// CAPTCHA invalid -> captcha login page
if ($validateCaptcha && !PhpCaptcha::Validate($f_captcha_code, true)) {
    return 'captcha';
}

// user valid, password invalid, encrypted, CAPTCHA valid -> upgrade
if (!is_null($user) && $f_is_encrypted && (strlen($user->getPasswordHash()) < 40)) {
    return 'upgrade';
}

// Everything else
return 'userpass';
